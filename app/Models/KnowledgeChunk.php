<?php
/** SQLite-backed semantic knowledge index. */
class KnowledgeChunk
{
    public static function ensureTable()
    {
        static $done = false;
        if ($done) return;
        Database::getInstance()->exec("CREATE TABLE IF NOT EXISTS knowledge_chunks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            source_type TEXT NOT NULL,
            source_id INTEGER,
            content TEXT NOT NULL,
            embedding TEXT NOT NULL,
            embedding_provider TEXT NOT NULL,
            embedding_model TEXT NOT NULL,
            embedding_dimension INTEGER NOT NULL,
            metadata TEXT NOT NULL DEFAULT '{}',
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE(source_type, source_id)
        )");
        $done = true;
    }

    public static function upsert($type, $id, $content, array $metadata, array $embedding)
    {
        self::ensureTable();
        $vector = $embedding['vector'] ?? [];
        $dimension = (int) ($embedding['dimension'] ?? count($vector));
        if (!$vector || $dimension !== count($vector)) throw new RuntimeException('Invalid embedding dimension.');
        $db = Database::getInstance();
        $sql = "INSERT INTO knowledge_chunks
            (source_type, source_id, content, embedding, embedding_provider, embedding_model, embedding_dimension, metadata, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, datetime('now'))
            ON CONFLICT(source_type, source_id) DO UPDATE SET content=excluded.content,
            embedding=excluded.embedding, embedding_provider=excluded.embedding_provider,
            embedding_model=excluded.embedding_model, embedding_dimension=excluded.embedding_dimension,
            metadata=excluded.metadata, updated_at=datetime('now')";
        $db->getPdo()->prepare($sql)->execute([$type, $id === null ? null : (int) $id, $content,
            json_encode(array_values($vector), JSON_THROW_ON_ERROR), $embedding['provider'], $embedding['model'],
            $dimension, json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
    }

    public static function delete($type, $id)
    {
        self::ensureTable();
        Database::getInstance()->getPdo()->prepare('DELETE FROM knowledge_chunks WHERE source_type = ? AND source_id = ?')->execute([$type, (int) $id]);
    }

    public static function candidates($provider, $model, $dimension)
    {
        self::ensureTable();
        return Database::getInstance()->fetchAll('SELECT * FROM knowledge_chunks WHERE embedding_provider = ? AND embedding_model = ? AND embedding_dimension = ?', [$provider, $model, (int) $dimension]);
    }
}
