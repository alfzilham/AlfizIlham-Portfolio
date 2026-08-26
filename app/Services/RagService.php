<?php
/** SQLite semantic retrieval using PHP cosine similarity. */
class RagService
{
    public static function isConfigured() { return true; }
    public static function sync($sourceType, $sourceId, $content, array $metadata, array $embedding) { KnowledgeChunk::upsert($sourceType, $sourceId, $content, $metadata, $embedding); }
    public static function delete($sourceType, $sourceId) { KnowledgeChunk::delete($sourceType, $sourceId); }
    public static function search(array $embedding, $limit = 5)
    {
        $vector = $embedding['vector'] ?? []; $dimension = (int) ($embedding['dimension'] ?? count($vector));
        if (!$vector || count($vector) !== $dimension) return [];
        $rows = KnowledgeChunk::candidates($embedding['provider'] ?? '', $embedding['model'] ?? '', $dimension); $matches = [];
        foreach ($rows as $row) {
            $candidate = json_decode($row['embedding'], true);
            if (!is_array($candidate) || count($candidate) !== $dimension) continue;
            $score = self::cosine($vector, $candidate); if ($score === null) continue;
            $row['similarity'] = $score; $matches[] = $row;
        }
        usort($matches, static fn($a, $b) => $b['similarity'] <=> $a['similarity']);
        return array_slice($matches, 0, max(1, min((int) $limit, 8)));
    }
    private static function cosine(array $a, array $b)
    {
        $dot = 0.0; $normA = 0.0; $normB = 0.0;
        foreach ($a as $i => $value) {
            if (!is_numeric($value) || !is_numeric($b[$i] ?? null)) return null;
            $x = (float) $value; $y = (float) $b[$i];
            if (!is_finite($x) || !is_finite($y)) return null;
            $dot += $x * $y; $normA += $x * $x; $normB += $y * $y;
        }
        if ($normA <= 0.0 || $normB <= 0.0) return null;
        return $dot / (sqrt($normA) * sqrt($normB));
    }
}
