<?php

class Games
{
	private \PDO $pdo;
    private Database $database;

	private $table = 'games';

	public function __construct()
	{
        $instance = Database::getInstance();
        $this->database = $instance;
		$this->pdo = $instance->getConnection();
	}

	public function getGames(): array
	{
		$games = $this->pdo->query('SELECT * FROM ' . $this->table . ' ORDER BY generation ASC');
		if (!$games) return [];
		return $games->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getGameById(int $id): ?array
	{
		$stmt = $this->pdo->prepare('SELECT * FROM ' . $this->table . ' WHERE id = :id');
		$stmt->execute(['id' => $id]);
		if (!$stmt) return null;
		return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
	}

	public function create(array $data): bool
	{
        $keys = array_keys($data);
		$stmt = $this->pdo->prepare('
			INSERT INTO ' . $this->table . ' (' . implode(', ', $keys) . ') VALUES 
            (:' . implode(', :', $keys) . ')
		');

		return $stmt->execute($data);
	}

	public function update(array $data, int $id): bool
	{
        $setClause = implode(', ', array_map(fn($key) => $key . ' = :' . $key, array_keys($data)));
		$stmt = $this->pdo->prepare('
				UPDATE ' . $this->table . ' SET ' . $setClause . ' WHERE id = :update_id
			');
		return $stmt->execute(array_merge($data, ['update_id' => $id]));
	}

    public function deleteById(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM ' . $this->table . ' WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}
