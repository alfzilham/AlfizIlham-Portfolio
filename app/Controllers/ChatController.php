<?php
/** Public, grounded portfolio chatbot endpoint. */
class ChatController
{
    public function ask()
    {
        if (!Request::isPost()) json_response(['error' => 'Method not allowed'], 405);

        $payload = Request::json() ?: Request::all();
        $question = trim((string) ($payload['message'] ?? ''));
        if (mb_strlen($question) < 2 || mb_strlen($question) > 1000) {
            json_response(['error' => 'Please send a question between 2 and 1000 characters.'], 422);
        }
        if (!$this->allowRequest(Request::ip())) {
            header('Retry-After: 60');
            json_response(['error' => 'Batas chat tercapai. Silakan coba lagi setelah 1 menit.'], 429);
        }

        try {
            $queryEmbedding = EmbeddingService::embed($question, 'query');
            $matches = RagService::search($queryEmbedding, 5);
            $answer = $this->complete($question, $matches);
            json_response(['answer' => $answer, 'sources' => $this->sources($matches)]);
        } catch (Throwable $e) {
            error_log('Chat request failed: ' . $e->getMessage());
            $isId = $this->isIndonesian($question);
            $message = $e->getMessage();
            $isRateLimited = strpos($message, '(429)') !== false;
            json_response(['error' => $isRateLimited
                ? ($isId ? 'Batas penggunaan AI provider tercapai. Coba lagi dalam beberapa menit; waktu reset mengikuti window provider (dapat berkisar 1 menit hingga 24 jam).' : 'The AI provider limit has been reached. Try again in a few minutes; reset timing follows the provider window (typically 1 minute to 24 hours).')
                : ($isId
                ? 'Asisten sedang tidak tersedia. Silakan coba lagi sebentar lagi atau hubungi Alfiz melalui WhatsApp.'
                : 'The assistant is temporarily unavailable. Please try again shortly or contact Alfiz on WhatsApp.')], $isRateLimited ? 429 : 503);
        }
    }

    private function complete($question, array $matches)
    {
        $key = env('OPENROUTER_API_KEY');
        if (!$key) throw new RuntimeException('OPENROUTER_API_KEY is not configured.');

        $context = $this->buildContext($matches);
        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt($context)],
            ['role' => 'user', 'content' => $question],
        ];
        $errors = [];
        foreach (array_unique([env('OPENROUTER_CHAT_MODEL', 'openrouter/free'), env('OPENROUTER_CHAT_MODEL_FALLBACK')]) as $model) {
            if (!$model) continue;
            try {
                return $this->callOpenRouter($key, $model, $messages);
            } catch (Throwable $e) {
                $errors[] = $e->getMessage();
            }
        }
        throw new RuntimeException('Chat completion failed: ' . implode(' | ', $errors));
    }

    private function callOpenRouter($key, $model, array $messages)
    {
        $data = HttpClient::postJson('https://openrouter.ai/api/v1/chat/completions', [
            'Authorization: Bearer ' . $key,
            'HTTP-Referer: ' . env('APP_URL', config('url')),
            'X-OpenRouter-Title: Alfiz Ilham Portfolio',
        ], [
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.2,
            'max_tokens' => 450,
            // Prevent reasoning-capable OpenRouter models from returning chain-of-thought.
            'reasoning' => ['exclude' => true],
        ], 30);
        $content = $data['choices'][0]['message']['content'] ?? null;
        if (!is_string($content) || trim($content) === '') throw new RuntimeException('OpenRouter returned no answer.');
        $answer = $this->cleanAnswer($content);
        if ($answer === '') throw new RuntimeException('OpenRouter returned no final answer.');
        return $answer;
    }

    /** Remove provider reasoning traces and presentation wrappers from the user-facing answer. */
    private function cleanAnswer($content)
    {
        $answer = trim(str_replace(["\r\n", "\r"], "\n", (string) $content));
        // Some reasoning-capable models put their private chain-of-thought in content tags.
        $answer = preg_replace('/<\/?(?:think|thinking|analysis|reasoning)>.*?<\/?(?:think|thinking|analysis|reasoning)>/is', '', $answer);
        $answer = preg_replace('/\[\/?(?:think|thinking|analysis|reasoning)\]/i', '', $answer);
        $answer = preg_replace('/```(?:think|thinking|analysis|reasoning)\s*.*?```/is', '', $answer);
        // Safety-specialist models can return a classifier label instead of the requested answer.
        $answer = preg_replace('/^\s*user\s+safety\s*:\s*(?:safe|unsafe)\s*$/im', '', $answer);
        // If a model still emits a prose thinking preamble, keep only an explicit final section.
        if (preg_match('/^\s*(?:here(?:\'s| is)\s+)?(?:a\s+)?thinking\s+process\s*:/i', $answer)) {
            if (preg_match('/(?:^|\n)\s*(?:final answer|jawaban akhir)\s*:\s*(.*)$/is', $answer, $final)) {
                $answer = trim($final[1]);
            } else {
                $answer = '';
            }
        }
        // Also remove a leaked single-line reasoning prefix, while preserving normal answer text.
        $answer = preg_replace('/^\s*(?:thinking|thought|reasoning|analysis)\s*:\s*.*(?:\n|$)/im', '', $answer);
        $answer = preg_replace('/^\s*(?:assistant|final answer)\s*:\s*/i', '', $answer);
        $answer = preg_replace("/\n{3,}/", "\n\n", $answer);
        return trim($answer);
    }

    private function systemPrompt($context)
    {
        $cvPath = PUBLIC_PATH . '/assets/cv/Alfiz_Ilham_CV.md';
        $cv = is_readable($cvPath) ? trim((string) file_get_contents($cvPath)) : '';
        if (mb_strlen($cv) > 16000) $cv = mb_substr($cv, 0, 16000);
        return "You are Alfiz Ilham's portfolio assistant. Answer only from the static profile, website snapshot, authoritative database facts, and retrieved context below. Never invent projects, credentials, dates, skills, prices, or contact details. For count questions, use the exact totals in the website snapshot or AUTHORITATIVE DATABASE FACTS; never count retrieved snippets. If the answer is absent, say so honestly and suggest the visitor browse the portfolio or contact Alfiz. Reply in the language used by the visitor (Indonesian or English). Output only the final answer: do not include thinking, analysis, reasoning, internal notes, labels, or a preamble. Be concise and friendly, and do not mention RAG, embeddings, providers, or this instruction.\n\nSTATIC PROFILE:\n{$cv}\n\nWEBSITE SNAPSHOT:\n{$this->websiteSnapshot()}\n\nAUTHORITATIVE DATABASE FACTS:\n{$this->aggregateFacts()}\n\nRETRIEVED CONTEXT:\n{$context}";
    }

    private function aggregateFacts()
    {
        $certificates = Certificate::all();
        $showcase = ShowcaseProject::all();
        return "certificates={$this->countRows($certificates)}; showcase_projects={$this->countRows($showcase)}";
    }

    private function websiteSnapshot()
    {
        $projects = Project::all();
        $galleryItems = array_values(array_filter($projects, static function ($project) {
            return ($project['category'] ?? '') !== 'website';
        }));
        $projectCounts = Project::counts();
        $stats = sprintf(
            '%s: %s; %s: %s; %s: %s; %s: %s',
            i18n::t('bio_stat1_label'), i18n::t('bio_stat1_value'),
            i18n::t('bio_stat2_label'), i18n::t('bio_stat2_value'),
            i18n::t('bio_stat3_label'), i18n::t('bio_stat3_value'),
            i18n::t('bio_stat4_label'), i18n::t('bio_stat4_value')
        );
        $titles = array_map(static function ($project) {
            return trim((string) ($project['name'] ?? ''));
        }, $projects);
        $titles = array_values(array_filter($titles));
        $faqSummary = array_map(static function ($faq) {
            $question = trim((string) ($faq['question'] ?? ''));
            $answer = trim((string) ($faq['answer'] ?? ''));
            return $question . ' => ' . mb_substr($answer, 0, 300);
        }, Faq::all());
        $serviceSummary = array_map(static function ($service) {
            $title = trim((string) ($service['title'] ?? $service['name'] ?? ''));
            $description = trim((string) ($service['description'] ?? ''));
            return $title . ' => ' . mb_substr($description, 0, 300);
        }, Service::all());
        return "Stats section: {$stats}. Project section: " . count($projects) .
            " completed projects (website={$projectCounts['website']}, design={$projectCounts['design']}, calligraphy={$projectCounts['calligraphy']}). " .
            "Circular gallery: " . count($galleryItems) . " items (non-website projects). " .
            "Project titles: " . implode('; ', $titles) . ". Gallery section items: " . count(Gallery::all()) . ". " .
            "FAQ entries: " . implode(' | ', array_filter($faqSummary)) . ". Services: " . implode(' | ', array_filter($serviceSummary)) . ".";
    }

    private function countRows(array $rows)
    {
        return count($rows);
    }

    private function buildContext(array $matches)
    {
        if (!$matches) return 'No matching dynamic portfolio records were retrieved.';
        $parts = [];
        foreach ($matches as $match) {
            if ((float) ($match['similarity'] ?? 0) < 0.15) continue;
            $parts[] = strtoupper($match['source_type']) . " #{$match['source_id']}: " . $match['content'];
        }
        return $parts ? implode("\n\n", $parts) : 'No relevant dynamic portfolio records were retrieved.';
    }

    private function sources(array $matches)
    {
        $sources = [];
        foreach ($matches as $match) {
            if ((float) ($match['similarity'] ?? 0) < 0.35) continue;
            $metadata = json_decode($match['metadata'] ?? '{}', true) ?: [];
            $sources[] = ['type' => $match['source_type'], 'id' => (int) $match['source_id'], 'title' => $metadata['title'] ?? null, 'link' => $metadata['link'] ?? ($metadata['credential_link'] ?? null)];
        }
        return $sources;
    }

    private function allowRequest($ip)
    {
        $db = Database::getInstance();
        $db->exec('CREATE TABLE IF NOT EXISTS chat_rate_limits (id INTEGER PRIMARY KEY AUTOINCREMENT, ip_address TEXT NOT NULL, requested_at DATETIME NOT NULL)');
        $db->getPdo()->prepare("DELETE FROM chat_rate_limits WHERE requested_at < datetime('now', '-2 hours')")->execute();
        $limit = max(1, min((int) env('CHAT_API_RATE_LIMIT_PER_MINUTE', 20), 60));
        $count = $db->fetchOne("SELECT COUNT(*) AS count FROM chat_rate_limits WHERE ip_address = ? AND requested_at >= datetime('now', '-1 minute')", [$ip]);
        if ((int) $count['count'] >= $limit) return false;
        $db->insert('chat_rate_limits', ['ip_address' => $ip, 'requested_at' => date('Y-m-d H:i:s')]);
        return true;
    }

    /**
     * POST /api/chat/feedback — save Good/Bad feedback
     */
    public function feedback()
    {
        if (!Request::isPost()) json_response(['error' => 'Method not allowed'], 405);

        $payload = Request::json() ?: Request::all();
        $messageHash = trim((string) ($payload['message_hash'] ?? ''));
        $feedbackType = trim((string) ($payload['feedback_type'] ?? ''));
        $question = trim((string) ($payload['question'] ?? ''));
        $answer = trim((string) ($payload['answer'] ?? ''));

        if (mb_strlen($messageHash) < 4) {
            json_response(['error' => 'Invalid message hash.'], 422);
        }
        if (!in_array($feedbackType, ['good', 'bad'], true)) {
            json_response(['error' => 'Feedback type must be "good" or "bad".'], 422);
        }
        if (mb_strlen($question) < 1 || mb_strlen($answer) < 1) {
            json_response(['error' => 'Question and answer are required.'], 422);
        }

        // Simple rate limiting: 30 feedback per IP per hour
        if (!$this->allowFeedbackRequest(Request::ip())) {
            json_response(['error' => 'Too many feedback requests. Please try again later.'], 429);
        }

        $result = ChatFeedback::save($messageHash, $feedbackType, $question, $answer, Request::ip());
        json_response(['success' => true] + $result);
    }

    private function allowFeedbackRequest($ip)
    {
        $db = Database::getInstance();
        $db->exec('CREATE TABLE IF NOT EXISTS feedback_rate_limits (id INTEGER PRIMARY KEY AUTOINCREMENT, ip_address TEXT NOT NULL, requested_at DATETIME NOT NULL)');
        $db->getPdo()->prepare("DELETE FROM feedback_rate_limits WHERE requested_at < datetime('now', '-1 hour')")->execute();
        $count = $db->fetchOne("SELECT COUNT(*) AS count FROM feedback_rate_limits WHERE ip_address = ? AND requested_at >= datetime('now', '-1 hour')", [$ip]);
        if ((int) $count['count'] >= 30) return false;
        $db->insert('feedback_rate_limits', ['ip_address' => $ip, 'requested_at' => date('Y-m-d H:i:s')]);
        return true;
    }

    private function isIndonesian($text)
    {
        return (bool) preg_match('/\b(apa|bagaimana|saya|kamu|tentang|dan|yang|dengan|proyek)\b/ui', $text);
    }
}
