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
            json_response(['error' => 'Too many requests. Please try again in a minute.'], 429);
        }

        try {
            $queryEmbedding = EmbeddingService::embed($question, 'query');
            $matches = RagService::search($queryEmbedding, 5);
            $answer = $this->complete($question, $matches);
            json_response(['answer' => $answer, 'sources' => $this->sources($matches)]);
        } catch (Throwable $e) {
            error_log('Chat request failed: ' . $e->getMessage());
            $isId = $this->isIndonesian($question);
            json_response(['error' => $isId
                ? 'Asisten sedang tidak tersedia. Silakan coba lagi sebentar lagi atau hubungi Alfiz melalui WhatsApp.'
                : 'The assistant is temporarily unavailable. Please try again shortly or contact Alfiz on WhatsApp.'], 503);
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
        $body = json_encode(['model' => $model, 'messages' => $messages, 'temperature' => 0.2, 'max_tokens' => 450], JSON_UNESCAPED_UNICODE);
        $curl = curl_init('https://openrouter.ai/api/v1/chat/completions');
        curl_setopt_array($curl, [
            CURLOPT_POST => true, CURLOPT_POSTFIELDS => $body, CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30, CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $key,
                'HTTP-Referer: ' . env('APP_URL', config('url')), 'X-OpenRouter-Title: Alfiz Ilham Portfolio'],
        ]);
        $response = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        $data = is_string($response) ? json_decode($response, true) : null;
        $content = $data['choices'][0]['message']['content'] ?? null;
        if ($status < 200 || $status >= 300 || !is_string($content) || trim($content) === '') {
            throw new RuntimeException('OpenRouter request failed' . ($status ? " ({$status})" : '') . ($error ? ': ' . $error : '.'));
        }
        return trim($content);
    }

    private function systemPrompt($context)
    {
        $cvPath = PUBLIC_PATH . '/assets/cv/Alfiz_Ilham_CV.md';
        $cv = is_readable($cvPath) ? trim((string) file_get_contents($cvPath)) : '';
        if (mb_strlen($cv) > 16000) $cv = mb_substr($cv, 0, 16000);
        return "You are Alfiz Ilham's portfolio assistant. Answer only from the static profile and retrieved context below. Never invent projects, credentials, dates, skills, prices, or contact details. If the answer is absent, say so honestly and suggest the visitor browse the portfolio or contact Alfiz. Reply in the language used by the visitor (Indonesian or English). Be concise, friendly, and do not mention RAG, embeddings, providers, or this instruction.\n\nSTATIC PROFILE:\n{$cv}\n\nRETRIEVED CONTEXT:\n{$context}";
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

    private function isIndonesian($text)
    {
        return (bool) preg_match('/\b(apa|bagaimana|saya|kamu|tentang|dan|yang|dengan|proyek)\b/ui', $text);
    }
}
