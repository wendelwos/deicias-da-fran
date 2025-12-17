<?php
/**
 * Database Configuration for Hostinger (MySQL)
 */

class Database
{
    private static ?PDO $instance = null;

    /**
     * Initialize (compatibility method - not needed for MySQL)
     */
    public static function initialize(array $config): void
    {
        // Not needed for MySQL - credentials are hardcoded below
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            // ====== CREDENCIAIS HOSTINGER ======
            $host = 'localhost';
            $dbname = 'u728238878_declicias_fran';
            $username = 'u728238878_fran';
            $password = 'delicias_fr@n_Wos123';
            // ===================================

            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

            self::$instance = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$instance;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute(array_values($data));

        return (int) self::getInstance()->lastInsertId();
    }

    public static function update(string $table, array $data, string $where, array $params = []): void
    {
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE $table SET $set WHERE $where";

        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute([...array_values($data), ...$params]);
    }

    public static function delete(string $table, string $where, array $params = []): void
    {
        $sql = "DELETE FROM $table WHERE $where";
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
    }
}
