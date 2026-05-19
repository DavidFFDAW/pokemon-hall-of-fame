<?php

class Games
{
	private \PDO $pdo;
	private $table = 'games';
	private $required = [
		'game_key',
		'game_name',
		'player_name'
	];

	public function __construct(Database $database)
	{
		$this->pdo = $database->getConnection();
	}

	public function checkRequiredFields(array $data): bool
	{
		foreach ($this->required as $field) {
			if (empty($data[$field])) throw new Exception("El campo '$field' es obligatorio.");
		}
		return true;
	}

	public function getGames(): array
	{
		$games = $this->pdo->query('SELECT * FROM ' . $this->table . ' ORDER BY created_at DESC');
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
		$this->checkRequiredFields($data);

		$stmt = $this->pdo->prepare('
			INSERT INTO ' . $this->table . ' (game_key, game_name, player_name, region, championed_at, created_at) 
			VALUES (:game_key, :game_name, :player_name, :region, :championed_at, :created_at)
		');

		return $stmt->execute([
			'game_key' => $data['game_key'],
			'game_name' => $data['game_name'],
			'player_name' => $data['player_name'],
			'region' => $data['region'] ?? null,
			'championed_at' => !empty($data['championed_at']) ? date('Y-m-d H:i:s', strtotime($data['championed_at'])) : null,
			'created_at' => date('Y-m-d H:i:s')
		]);
	}
	public function update(array $data): bool
	{
		$this->checkRequiredFields($data);

		$stmt = $this->pdo->prepare('
				UPDATE ' . $this->table . ' SET 
					game_key = :game_key, 
					game_name = :game_name, 
					player_name = :player_name,
					region = :region,
					championed_at = :championed_at
				WHERE id = :id
			');
		return $stmt->execute([
			'id' => $data['id'],
			'game_key' => $data['game_key'],
			'game_name' => $data['game_name'],
			'player_name' => $data['player_name'],
			'region' => $data['region'] ?? null,
			'championed_at' => !empty($data['championed_at']) ? date('Y-m-d H:i:s', strtotime($data['championed_at'])) : null
		]);
	}

	public function upsert(array $data): bool
	{
		$this->checkRequiredFields($data);

		if ((!isset($data['id']) || empty($data['id'])) && $data['action'] === 'create')
			return $this->create($data);

		if ($data['action'] === 'update')
			return $this->update($data);

		throw new Exception('Acción no válida para upsert: ' . ($data['action'] ?? 'null'));
	}
}
