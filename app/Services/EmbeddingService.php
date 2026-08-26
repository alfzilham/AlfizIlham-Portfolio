<?php
/** Creates embeddings using Hugging Face, with OpenRouter as a fallback. */
class EmbeddingService
{
    public static function embed($text, $purpose = 'query')
    {
        $text = trim((string) $text);
        if ($text === '') throw new InvalidArgumentException('Text to embed is required.');

        $errors = [];
        try {
            return self::embedWithHuggingFace($text, $purpose);
        } catch (Throwable $e) {
            $errors[] = $e->getMessage();
        }

        try {
            return self::embedWithOpenRouter($text);
        } catch (Throwable $e) {
            $errors[] = $e->getMessage();
        }

        throw new RuntimeException('Embedding provider unavailable: ' . implode(' | ', $errors));
    }

    private static function embedWithHuggingFace($text, $purpose)
    {
        $token = env('HF_TOKEN');
        if (!$token) throw new RuntimeException('Hugging Face is not configured.');

        $model = env('HF_EMBEDDING_MODEL', 'BAAI/bge-m3');
        $url = str_replace('{model}', $model, env('HF_EMBEDDING_URL', 'https://router.huggingface.co/hf-inference/models/{model}'));
        $payload = ['inputs' => $text, 'normalize' => true];
        // bge-m3 accepts query/document prefixes; keeping this explicit improves retrieval consistency.
        if ($purpose === 'query') $payload['inputs'] = 'query: ' . $text;
        if ($purpose === 'document') $payload['inputs'] = 'passage: ' . $text;

        $data = self::request($url, ['Authorization: Bearer ' . $token], $payload, 20);
        $vector = self::normaliseVector($data);
        return ['vector' => $vector, 'provider' => 'huggingface', 'model' => $model, 'dimension' => count($vector)];
    }

    private static function embedWithOpenRouter($text)
    {
        $key = env('OPENROUTER_API_KEY');
        if (!$key) throw new RuntimeException('OpenRouter is not configured.');

        $model = env('OPENROUTER_EMBEDDING_MODEL', 'openai/text-embedding-3-small');
        $data = self::request('https://openrouter.ai/api/v1/embeddings', [
            'Authorization: Bearer ' . $key,
            'HTTP-Referer: ' . env('APP_URL', config('url')),
            'X-OpenRouter-Title: Alfiz Ilham Portfolio',
        ], ['model' => $model, 'input' => $text], 15);
        $vector = $data['data'][0]['embedding'] ?? null;
        if (!is_array($vector)) throw new RuntimeException('OpenRouter returned no embedding.');
        $vector = self::normaliseVector($vector);
        return ['vector' => $vector, 'provider' => 'openrouter', 'model' => $model, 'dimension' => count($vector)];
    }

    private static function normaliseVector($response)
    {
        $vector = isset($response[0]) && is_array($response[0]) ? $response[0] : $response;
        if (!is_array($vector) || count($vector) < 8) throw new RuntimeException('Embedding response is invalid.');
        return array_map('floatval', $vector);
    }

    private static function request($url, $headers, $payload, $timeout)
    {
        return HttpClient::postJson($url, $headers, $payload, $timeout);
    }
}
