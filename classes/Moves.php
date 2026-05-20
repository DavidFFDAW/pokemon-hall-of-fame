<?php

class Moves
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

	public function getMovesForPokemonVersion(int $poke_id, string $version): array
	{
		$moveList = [];
		$tr = json_decode(file_get_contents(BASE_PATH . '/moves.json'), true);
		$apiMoves = PokeApi::getPokemonMoves((string) $poke_id);

		foreach ($apiMoves as $move) {
			$moveID = basename($move['move']['url'] ?? '');
			$moveName = $move['move']['name'] ?? 'Unknown';
			$moveDatas = $tr[$moveName] ?? null;
			if (empty($moveID) || empty($moveName)) continue;
			if (!isset($moveDatas)) continue;

			foreach ($move['version_group_details'] as $versionGroup) {
				if ($versionGroup['version_group']['name'] !== $version) continue;
				$moveList[$move['move']['name']] = array_merge([
					'id' => $moveID,
				], $moveDatas);
			}
		}

		return $moveList;
	}

	public function getStoredMoveNamesForPokemon(int $poke_id): array
	{
		$stmt = $this->pdo->prepare('SELECT m.og_name FROM moves m WHERE m.pokemon_id = :poke_id');
		$stmt->execute(['poke_id' => $poke_id]);
		if (!$stmt) return [];

		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return !empty($result) ? array_column($result, 'og_name') : [];
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

	private function deleteOldMoves(int $pokemon_id): bool
	{
		if (empty($pokemon_id)) return true; // No hay ID, no hay movimientos que eliminar

		$stmt = $this->pdo->prepare('DELETE FROM moves WHERE pokemon_id = :pokemon_id');
		return $stmt->execute(['pokemon_id' => $pokemon_id]);
	}

	public function upsert(array $data): bool
	{
		$datamoves = array();
		$moves = $data['moves'] ?? [];
		$pokemonID = $data['pokemon_id'] ?? null;
		if (empty($moves) || empty($pokemonID)) return false;

		$tr = json_decode(file_get_contents(BASE_PATH . '/moves.json'), true);
		$this->deleteOldMoves($pokemonID);
	
		foreach ($moves as $en) {
			$datamoves[$en] = $tr[$en] ?? null;
		}

		$results = [];
		$stmt = $this->pdo->prepare('INSERT INTO moves (name, og_name, type, power, accuracy, pp, effect, pokemon_id) VALUES (:name, :og_name, :type, :power, :accuracy, :pp, :effect, :pokemon_id)');
		foreach ($datamoves as $en => $move) {
			$results[] = $stmt->execute([
				'name' => $move['name'] ?? null,
				'og_name' => $en ?? null,
				'type' => $move['type'] ?? null,
				'power' => $move['power'] ?? null,
				'accuracy' => $move['accuracy'] ?? null,
				'pp' => $move['pp'] ?? null,
				'effect' => $move['effect'] ?? null,
				'pokemon_id' => $pokemonID
			]);
		}

		return !in_array(false, $results, true);
	}
}
