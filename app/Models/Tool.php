<?php
/**
 * Tool Model
 */
class Tool
{
    /**
     * Get all tools
     */
    public static function all()
    {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM tools ORDER BY sort_order ASC, name ASC");
    }

    /**
     * Get tools by category
     */
    public static function byCategory($category)
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT * FROM tools WHERE category = ? ORDER BY sort_order ASC, name ASC",
            [$category]
        );
    }

    /**
     * Search tools by name
     */
    public static function search($query)
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT * FROM tools WHERE name LIKE ? ORDER BY sort_order ASC, name ASC",
            ["%{$query}%"]
        );
    }

    /**
     * Get tools by category and search
     */
    public static function filtered($category = 'all', $search = '')
    {
        $db = Database::getInstance();
        $sql = "SELECT * FROM tools WHERE 1=1";
        $params = [];

        if ($category !== 'all') {
            $sql .= " AND category = ?";
            $params[] = $category;
        }

        if ($search !== '') {
            $sql .= " AND name LIKE ?";
            $params[] = "%{$search}%";
        }

        $sql .= " ORDER BY sort_order ASC, name ASC";
        return $db->fetchAll($sql, $params);
    }
}
