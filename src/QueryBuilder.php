<?php

require_once "Database.php";

class QueryBuilder
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function fetchOne(string $query, array $params = []): ?object
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }

    public function fetchAll(string $query, array $params = []): array
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function fetchIterator(string $query, array $params = []): Generator
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        while ($row = $stmt->fetch()) {
            yield $row;
        }
    }

    public function execute(string $query, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($params);
    }

    public function insert(string $table, array $data): bool
    {
        $fields = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_map(fn($key) => ":$key", array_keys($data)));
        $query = "INSERT INTO $table ($fields) VALUES ($placeholders)";

        return $this->execute($query, $data);
    }

    public function update(string $table, array $data, string $condition, array $conditionParams): bool
    {
        $setClause = implode(", ", array_map(fn($key) => "$key = :$key", array_keys($data)));
        $query = "UPDATE $table SET $setClause WHERE $condition";
        return $this->execute($query, array_merge($data, $conditionParams));
    }

    public function beginTransaction(): bool
    {
        return Database::beginTransaction();
    }

    public function commit(): bool
    {
        return Database::commit();
    }

    public function rollback(): bool
    {
        return Database::rollback();
    }
}
