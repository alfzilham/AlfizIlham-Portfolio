<?php
/**
 * ChatFeedback Model — stores Good/Bad feedback on assistant responses
 */
class ChatFeedback
{
    public static function ensureTable()
    {
        static $done = false;
        if ($done) return;
        $db = Database::getInstance();
        $db->exec("
            CREATE TABLE IF NOT EXISTS chat_feedback (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                message_hash TEXT NOT NULL,
                feedback_type TEXT NOT NULL CHECK(feedback_type IN ('good', 'bad')),
                question TEXT NOT NULL,
                answer TEXT NOT NULL,
                ip_address TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        $done = true;
    }

    public static function save($messageHash, $feedbackType, $question, $answer, $ip)
    {
        self::ensureTable();
        $db = Database::getInstance();
        // Check if feedback already exists for this message hash
        $existing = $db->fetchOne(
            "SELECT id, feedback_type FROM chat_feedback WHERE message_hash = ? LIMIT 1",
            [$messageHash]
        );
        if ($existing) {
            if ($existing['feedback_type'] === $feedbackType) {
                // Toggle off — remove the feedback
                $db->getPdo()->prepare("DELETE FROM chat_feedback WHERE id = ?")->execute([(int) $existing['id']]);
                return ['action' => 'removed', 'type' => null];
            }
            // Switch feedback type
            $db->getPdo()->prepare(
                "UPDATE chat_feedback SET feedback_type = ?, created_at = ? WHERE id = ?"
            )->execute([$feedbackType, date('Y-m-d H:i:s'), (int) $existing['id']]);
            return ['action' => 'updated', 'type' => $feedbackType];
        }
        // Insert new feedback
        $db->insert('chat_feedback', [
            'message_hash' => $messageHash,
            'feedback_type' => $feedbackType,
            'question' => $question,
            'answer' => $answer,
            'ip_address' => $ip,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return ['action' => 'saved', 'type' => $feedbackType];
    }

    public static function findByHash($messageHash)
    {
        self::ensureTable();
        return Database::getInstance()->fetchOne(
            "SELECT * FROM chat_feedback WHERE message_hash = ? LIMIT 1",
            [$messageHash]
        );
    }

    public static function count()
    {
        self::ensureTable();
        return Database::getInstance()->count('chat_feedback');
    }
}
