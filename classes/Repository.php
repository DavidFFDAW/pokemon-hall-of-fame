<?php

abstract class Repository
{
    protected \PDO $pdo;
    protected $table = '';
    protected $requiredFields = [];

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    public function getRequiredFields(): array
    {
        return $this->requiredFields;
    }

    public function get($extra_sql = ''): array
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} $extra_sql");
        if (!$stmt) return [];

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSingle($extra_sql = ''): array
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} $extra_sql LIMIT 1");
        if (!$stmt) return [];

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function find(int $id, $extra_sql = ''): array
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} WHERE id = $id $extra_sql LIMIT 1");
        if (!$stmt) return [];

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function paginate(int $page = 1, int $perPage = 10, $extra_sql = ''): array
    {
        $offset = ($page - 1) * $perPage;
        // we want to have also the total count of items for pagination purposes
        $stmt = $this->pdo->query("SELECT SQL_CALC_FOUND_ROWS * FROM {$this->table} $extra_sql LIMIT $perPage OFFSET $offset");
        if (!$stmt) return [];

        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalStmt = $this->pdo->query("SELECT FOUND_ROWS() as total");
        $total = $totalStmt ? (int) $totalStmt->fetch(PDO::FETCH_ASSOC)['total'] : 0;

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage),
        ];
    }

    public function query(string $sql): array
    {
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt) return [];

        $res = $stmt->execute();
        if (!$res) return [];

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function prepare(string $sql): PDOStatement
    {
        return $this->pdo->prepare($sql);
    }

    public function select(string $sql): array
    {
        $stmt = $this->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRow(string $sql, array $params = []): ?array
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function insert(array $datas = []): int
    {
        if ($datas === []) return 0;
        $keys = array_keys($datas);
        $columns = implode(', ', $keys);
        $placeholders = ':' . implode(', :', $keys);
        $stmt = $this->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
        $stmt->execute($datas);

        return (int) $this->getConnection()->lastInsertId();
    }

    public function update(array $datas = [], $id = null): int
    {
        if ($id === null || $datas === []) return 0;

        $set = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($datas)));
        $sql = "UPDATE {$this->table} SET $set WHERE id = :update_id";
        $stmt = $this->prepare($sql);
        $params = array_merge($datas, ['update_id' => $id]);
        $stmt->execute($params);

        return $stmt->rowCount();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->prepare("DELETE FROM {$this->table} WHERE id = :delete_id");
        $stmt->execute(['delete_id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
