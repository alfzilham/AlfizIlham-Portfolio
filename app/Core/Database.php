<?php
/**
 * Database — PDO SQLite Connection Singleton
 */
class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $dbPath = DATA_PATH . '/database.sqlite';
        $this->pdo = new PDO('sqlite:' . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * Execute a query and return all results
     */
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Execute a query and return one result
     */
    public function fetchOne($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Execute a query and return row count
     */
    public function count($table, $where = '1=1', $params = [])
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as cnt FROM {$table} WHERE {$where}");
        $stmt->execute($params);
        return (int) $stmt->fetch()['cnt'];
    }

    /**
     * Insert a row
     */
    public function insert($table, $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->pdo->lastInsertId();
    }

    /**
     * Run raw SQL (for setup/seeding)
     */
    public function exec($sql)
    {
        return $this->pdo->exec($sql);
    }
}
