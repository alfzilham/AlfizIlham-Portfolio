<?php
/**
 * Certificate Model
 */
class Certificate
{
    /**
     * Ensure table exists with all columns (lazy migration)
     */
    public static function ensureTable()
    {
        static $done = false;
        if ($done) return;
        $db = Database::getInstance();
        $db->exec("
            CREATE TABLE IF NOT EXISTS certificates (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                company TEXT,
                credential_id TEXT,
                credential_link TEXT,
                image TEXT NOT NULL,
                sort_order INTEGER DEFAULT 0,
                pinned INTEGER NOT NULL DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        // Add company column if missing (for older databases)
        $hasCompany = false;
        foreach ($db->fetchAll("PRAGMA table_info(certificates)") as $col) {
            if ($col['name'] === 'company') { $hasCompany = true; break; }
        }
        if (!$hasCompany) {
            $db->exec("ALTER TABLE certificates ADD COLUMN company TEXT");
        }
        $hasPinned = false;
        foreach ($db->fetchAll("PRAGMA table_info(certificates)") as $col) {
            if ($col['name'] === 'pinned') { $hasPinned = true; break; }
        }
        if (!$hasPinned) $db->exec("ALTER TABLE certificates ADD COLUMN pinned INTEGER NOT NULL DEFAULT 0");
        $done = true;
    }

    /**
     * Get all certificates
     */
    public static function all()
    {
        self::ensureTable();
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM certificates ORDER BY pinned DESC, sort_order ASC, id ASC");
    }

    /**
     * Find one by ID
     */
    public static function find($id)
    {
        self::ensureTable();
        $db = Database::getInstance();
        return $db->fetchOne("SELECT * FROM certificates WHERE id = ?", [(int) $id]);
    }

    /**
     * Create a new certificate
     */
    public static function create($title, $company, $credentialId, $credentialLink, $image)
    {
        self::ensureTable();
        $db = Database::getInstance();
        return $db->insert('certificates', [
            'title' => $title,
            'company' => $company,
            'credential_id' => $credentialId,
            'credential_link' => $credentialLink,
            'image' => $image,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Update an existing certificate
     */
    public static function update($id, $title, $company, $credentialId, $credentialLink, $image = null)
    {
        self::ensureTable();
        $db = Database::getInstance();
        if ($image !== null) {
            return $db->getPdo()->prepare(
                "UPDATE certificates SET title = ?, company = ?, credential_id = ?, credential_link = ?, image = ? WHERE id = ?"
            )->execute([$title, $company, $credentialId, $credentialLink, $image, (int) $id]);
        }
        return $db->getPdo()->prepare(
            "UPDATE certificates SET title = ?, company = ?, credential_id = ?, credential_link = ? WHERE id = ?"
        )->execute([$title, $company, $credentialId, $credentialLink, (int) $id]);
    }

    /**
     * Delete a certificate
     */
    public static function delete($id)
    {
        self::ensureTable();
        $db = Database::getInstance();
        return $db->getPdo()->prepare("DELETE FROM certificates WHERE id = ?")->execute([(int) $id]);
    }

    public static function togglePinned($id)
    {
        self::ensureTable();
        $db = Database::getInstance();
        $db->getPdo()->prepare("UPDATE certificates SET pinned = CASE WHEN pinned = 1 THEN 0 ELSE 1 END WHERE id = ?")->execute([(int) $id]);
        return self::find($id);
    }
}
