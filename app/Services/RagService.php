<?php
/** pgvector-backed knowledge index. SQLite remains the source of truth. */
class RagService
{
    private static $pdo;

    public static function isConfigured()
    {
        return extension_loaded('pdo_pgsql') && (bool) env('NEON_DATABASE_URL');
    }

    public static function sync($sourceType, $sourceId, $content, array $metadata, array $embedding)
    {
        $pdo = self::connection();
        self::ensureSchema($pdo);
        $sql = "INSERT INTO knowledge_chunks (source_type, source_id, content, embedding, embedding_provider, embedding_model, metadata, updated_at)
                VALUES (:type, :id, :content, CAST(:embedding AS vector), :provider, :model, CAST(:metadata AS jsonb), NOW())
                ON CONFLICT (source_type, source_id) DO UPDATE SET
                    content = EXCLUDED.content, embedding = EXCLUDED.embedding,
                    embedding_provider = EXCLUDED.embedding_provider, embedding_model = EXCLUDED.embedding_model,
                    metadata = EXCLUDED.metadata, updated_at = NOW()";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':type' => $sourceType, ':id' => (int) $sourceId, ':content' => $content,
            ':embedding' => self::vectorLiteral($embedding['vector']), ':provider' => $embedding['provider'],
            ':model' => $embedding['model'], ':metadata' => json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }

    public static function delete($sourceType, $sourceId)
    {
        $stmt = self::connection()->prepare('DELETE FROM knowledge_chunks WHERE source_type = ? AND source_id = ?');
        $stmt->execute([$sourceType, (int) $sourceId]);
    }

    public static function search(array $embedding, $limit = 5)
    {
        $pdo = self::connection();
        self::ensureSchema($pdo);
        $limit = max(1, min((int) $limit, 8));
        // Vectors from different providers are never compared. This keeps fallback retrieval semantically valid.
        $sql = "SELECT source_type, source_id, content, metadata,
                1 - (embedding <=> CAST(? AS vector)) AS similarity
                FROM knowledge_chunks WHERE embedding_provider = ?
                ORDER BY embedding <=> CAST(? AS vector) LIMIT {$limit}";
        $stmt = $pdo->prepare($sql);
        $vector = self::vectorLiteral($embedding['vector']);
        $stmt->execute([$vector, $embedding['provider'], $vector]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function ensureSchema(PDO $pdo = null)
    {
        $pdo = $pdo ?: self::connection();
        $pdo->exec('CREATE EXTENSION IF NOT EXISTS vector');
        $pdo->exec("CREATE TABLE IF NOT EXISTS knowledge_chunks (
            id BIGSERIAL PRIMARY KEY, source_type TEXT NOT NULL, source_id INTEGER NOT NULL,
            content TEXT NOT NULL, embedding VECTOR NOT NULL, embedding_provider TEXT NOT NULL,
            embedding_model TEXT NOT NULL, metadata JSONB NOT NULL DEFAULT '{}'::jsonb,
            updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(), UNIQUE(source_type, source_id))");
        $pdo->exec('CREATE INDEX IF NOT EXISTS knowledge_chunks_source_idx ON knowledge_chunks (source_type, source_id)');
        $pdo->exec('CREATE INDEX IF NOT EXISTS knowledge_chunks_provider_idx ON knowledge_chunks (embedding_provider)');
    }

    private static function connection()
    {
        if (self::$pdo instanceof PDO) return self::$pdo;
        if (!extension_loaded('pdo_pgsql')) throw new RuntimeException('PDO PostgreSQL extension is unavailable.');
        $url = env('NEON_DATABASE_URL');
        if (!$url) throw new RuntimeException('NEON_DATABASE_URL is not configured.');
        self::$pdo = new PDO(self::pdoDsn($url), null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 10]);
        return self::$pdo;
    }

    private static function pdoDsn($url)
    {
        if (strpos($url, 'pgsql:') === 0) return $url;
        $parts = parse_url($url);
        if (!$parts || empty($parts['host']) || empty($parts['path'])) {
            throw new RuntimeException('NEON_DATABASE_URL must be a PostgreSQL URL or PDO DSN.');
        }
        parse_str($parts['query'] ?? '', $query);
        $items = [
            'host=' . $parts['host'], 'port=' . ($parts['port'] ?? 5432),
            'dbname=' . ltrim($parts['path'], '/'), 'user=' . rawurldecode($parts['user'] ?? ''),
            'password=' . rawurldecode($parts['pass'] ?? ''), 'sslmode=' . ($query['sslmode'] ?? 'require'),
        ];
        // Older libpq builds (including some XAMPP releases) need Neon SNI's
        // endpoint option explicitly because they cannot infer it from TLS.
        if (preg_match('/^(ep-.+?)(?:-pooler)?\./', $parts['host'], $match)) {
            $items[] = 'options=endpoint=' . $match[1];
        }
        return 'pgsql:' . implode(';', $items);
    }

    private static function vectorLiteral(array $vector)
    {
        return '[' . implode(',', array_map(static fn($value) => sprintf('%.10F', (float) $value), $vector)) . ']';
    }
}
