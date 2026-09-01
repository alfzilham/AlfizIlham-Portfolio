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
        $db->getPdo()->exec("DELETE FROM tools WHERE name IN ('Google Developer Program', 'Google Apps Script', 'Google Cloud', 'Firebase', 'Amazon Bedrock', 'AWS Bedrock', 'Vertex AI', 'Scratch')");
        $googleTools = [
            ['Antigravity', 'ai-ml', 'AI & ML', 38],
            ['Google AI Studio', 'ai-ml', 'AI & ML', 39],
            ['Stitch', 'ai-ml', 'AI & ML', 55],
        ];
        foreach ($googleTools as [$name, $category, $label, $sort]) {
            $icon = match ($name) {
                'Antigravity' => 'assets/image/icons/ai/antigravity.svg',
                'Google AI Studio' => 'assets/image/icons/ai/google-ai-studio.svg',
                'Stitch' => 'assets/image/icons/ai/stitch.svg',
                default => 'assets/image/icons/ai/google-colab.svg',
            };
            $stmt = $db->getPdo()->prepare("INSERT INTO tools (name, category, category_label, icon, sort_order) SELECT :name, :category, :label, :icon, :sort WHERE NOT EXISTS (SELECT 1 FROM tools WHERE lower(name) = lower(:name_check))");
            $stmt->execute(['name' => $name, 'category' => $category, 'label' => $label, 'icon' => $icon, 'sort' => $sort, 'name_check' => $name]);
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
