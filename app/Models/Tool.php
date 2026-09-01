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
        $googleTools = [
            ['Google Developer Program', 'tools', 'Developer Program', 57],
            ['Google Apps Script', 'tools', 'Automation', 58],
            ['Google Cloud', 'devops', 'Cloud Platform', 59],
            ['Firebase', 'backend', 'Backend Platform', 60],
            ['Google AI Studio', 'ai', 'AI & ML', 61],
        ];
        $stmt = $db->prepare("INSERT INTO tools (name, category, category_label, icon, sort_order) SELECT :name, :category, :label, 'assets/image/icons/ai/google-colab.svg', :sort WHERE NOT EXISTS (SELECT 1 FROM tools WHERE lower(name) = lower(:name_check))");
        foreach ($googleTools as [$name, $category, $label, $sort]) {
            $stmt->execute(['name' => $name, 'category' => $category, 'label' => $label, 'sort' => $sort, 'name_check' => $name]);
        }
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
