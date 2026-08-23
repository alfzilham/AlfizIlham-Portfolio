<?php
/**
 * Showcase Project Model — admin-curated cards for the ChromaGrid
 */
class ShowcaseProject
{
    /**
     * Ensure table exists (lazy migration)
     */
    public static function ensureTable()
    {
        static $done = false;
        if ($done) return;
        $db = Database::getInstance();
        $db->exec("
            CREATE TABLE IF NOT EXISTS showcase_projects (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT NOT NULL,
                image TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        $done = true;
    }

    /**
     * Get all showcase projects (newest first)
     */
    public static function all()
    {
        self::ensureTable();
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM showcase_projects ORDER BY id DESC");
    }

    /**
     * Find one by ID
     */
    public static function find($id)
    {
        self::ensureTable();
        $db = Database::getInstance();
        return $db->fetchOne("SELECT * FROM showcase_projects WHERE id = ?", [(int) $id]);
    }

    /**
     * Create a new showcase project
     */
    public static function create($title, $description, $image)
    {
        self::ensureTable();
        $db = Database::getInstance();
        return $db->insert('showcase_projects', [
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Update an existing showcase project
     */
    public static function update($id, $title, $description, $image = null)
    {
        self::ensureTable();
        $db = Database::getInstance();
        if ($image !== null) {
            return $db->getPdo()->prepare(
                "UPDATE showcase_projects SET title = ?, description = ?, image = ? WHERE id = ?"
            )->execute([$title, $description, $image, (int) $id]);
        }
        return $db->getPdo()->prepare(
            "UPDATE showcase_projects SET title = ?, description = ? WHERE id = ?"
        )->execute([$title, $description, (int) $id]);
    }

    /**
     * Delete a showcase project
     */
    public static function delete($id)
    {
        self::ensureTable();
        $db = Database::getInstance();
        return $db->getPdo()->prepare("DELETE FROM showcase_projects WHERE id = ?")->execute([(int) $id]);
    }
}
