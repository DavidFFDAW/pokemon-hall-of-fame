<?php

class Pokemon
{
	private \PDO $pdo;
	private $table = 'pokemons';
	private $required = [
		'nickname',
		'level',
		'game_id',
		'pokemon_id'
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

	public function getPokemons(): array
	{
		$pokemons = $this->pdo->query('SELECT p.*, g.game_name FROM ' . $this->table . ' p INNER JOIN games g ON p.game_id = g.id ORDER BY p.game_id ASC');
		if (!$pokemons) return [];
		return $pokemons->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getHowManyPokemonsByGameId(int $gameId): int
	{
		$stmt = $this->pdo->prepare('SELECT * FROM ' . $this->table . ' WHERE game_id = :game_id ORDER BY created_at DESC');
		$stmt->execute(['game_id' => $gameId]);
		if (!$stmt) return 0;
		return $stmt->rowCount();
	}

	public function getPokemonById(int $id): ?array
	{
		$stmt = $this->pdo->prepare('SELECT * FROM ' . $this->table . ' WHERE id = :id');
		$stmt->execute(['id' => $id]);
		if (!$stmt) return null;

		return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
	}

	public function getPokemonByIdWithGame(int $id): ?array
	{
		$stmt = $this->pdo->prepare('SELECT p.*, g.game_key FROM ' . $this->table . ' p INNER JOIN games g ON p.game_id = g.id WHERE p.id = :id');
		$stmt->execute(['id' => $id]);
		if (!$stmt) return null;

		return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
	}

	public function upsert(array $data, ?array $savedPokemon = null): bool
	{
		$this->checkRequiredFields($data);
		$poke = PokeApi::getPokemonById($data['pokemon_id']);
		$pokeData = array(
			'id' => $poke['id'] ?? 0,
			'name' => $poke['name'] ?? '',
			'species' => $poke['species']['name'] ?? '',
			'types' => array_map(fn($type) => $type['type']['name'], $poke['types'] ?? [])
		);

		if ((!isset($data['id']) || empty($data['id'])) && $data['action'] === 'create') {
			$stmt = $this->pdo->prepare('
				INSERT INTO ' . $this->table . ' (poke_id, name, nickname, species, types, level, game_id, created_at) 
				VALUES (:poke_id, :name, :nickname, :species, :types, :level, :game_id, :created_at)
			');
			return $stmt->execute([
				'poke_id' => $pokeData['id'],
				'name' => $pokeData['name'],
				'nickname' => $data['nickname'],
				'species' => $pokeData['species'] ?? '',
				'types' => implode('-', $pokeData['types'] ?? []),
				'level' => $data['level'],
				'game_id' => $data['game_id'],
				'created_at' => date('Y-m-d H:i:s')
			]);
		}


		if ($data['action'] === 'update') {
			$stmt = $this->pdo->prepare('
				UPDATE ' . $this->table . ' SET 
					poke_id = :poke_id, 
					name = :name, 
					nickname = :nickname, 
					species = :species, 
					types = :types, 
					level = :level
				WHERE id = :id
			');
			return $stmt->execute([
				'poke_id' => $pokeData['id'],
				'name' => $pokeData['name'],
				'nickname' => $data['nickname'],
				'species' => $pokeData['species'] ?? '',
				'types' => implode('-', $pokeData['types'] ?? []),
				'level' => $data['level'],
				'id' => $data['id']
			]);
		}

		throw new Exception('Acción no válida para upsert: ' . ($data['action'] ?? 'null'));
	}
}
