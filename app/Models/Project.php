<?php
/**
 * Project Model
 */
class Project
{
    /**
     * Get all projects
     */
    public static function all()
    {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM projects ORDER BY sort_order ASC, id ASC");
    }

    /**
     * Get projects by category
     */
    public static function byCategory($category)
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT * FROM projects WHERE category = ? ORDER BY sort_order ASC, id ASC",
            [$category]
        );
    }

    /**
     * Get a single project by ID
     */
    public static function find($id)
    {
        $db = Database::getInstance();
        return $db->fetchOne("SELECT * FROM projects WHERE id = ?", [$id]);
    }

    /**
     * Get project counts by category
     */
    public static function counts()
    {
        $db = Database::getInstance();
        $rows = $db->fetchAll("SELECT category, COUNT(*) as count FROM projects GROUP BY category");
        $counts = ['website' => 0, 'design' => 0, 'calligraphy' => 0];
        foreach ($rows as $row) {
            $counts[$row['category']] = (int) $row['count'];
        }
        return $counts;
    }
}
