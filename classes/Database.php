<?php

class Database
{
    protected \PDO $pdo;
    protected static ?self $instance = null;

    private function __construct()
    {
        // sqlite with pdo
        // $this->pdo = new PDO('sqlite:' . STORAGE_PATH . '/hall_of_fame.sqlite');
        // $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // $this->pdo->exec('PRAGMA encoding = "UTF-8";');
        // $this->pdo->exec('PRAGMA foreign_keys = ON;');

        $this->pdo = new PDO('mysql:host=localhost;dbname=poke-hof', 'root', '');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('SET NAMES utf8mb4');
        $this->pdo->exec('SET CHARACTER SET utf8mb4');
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    public function query(string $sql): PDOStatement
    {
        $scaped = str_replace([';', '--'], '', $sql);
        return $this->pdo->query($scaped);
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
    
	public function getGamesWithPokemons() {
		$games = [];
		$pokemon_map = [];
		
		$pokemons_sql = $this->pdo->query('SELECT * FROM pokemons ORDER BY game_id DESC');
		$games_sql = $this->pdo->query('SELECT * FROM games ORDER BY championed_at DESC');
		$pokemons = $pokemons_sql->fetchAll(PDO::FETCH_ASSOC);
		$games_result = $games_sql->fetchAll(PDO::FETCH_ASSOC);

		foreach ($pokemons as $pokemon) {
			$pokemon['sprite'] = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/' . ($pokemon['poke_id'] ?? '0') . '.png';
			$pokemon['image'] = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/home/' . ($pokemon['poke_id'] ?? '0') . '.png';
			$pokemon_map[$pokemon['game_id']][] = $pokemon;
		}
		foreach ($games_result as $game) {
			$currentGamePokemons = $pokemon_map[$game['id']] ?? [];
			$games[] = array_merge($game, ['pokemons' => $currentGamePokemons]);
		}

		return $games;
	}

	public function getHomeRequest()
	{
		$games = [];
		$pokemon_map = [];
		$move_map = [];

		$pokemons_sql = $this->pdo->query('SELECT * FROM pokemons ORDER BY game_id DESC');
		$games_sql = $this->pdo->query('SELECT * FROM games ORDER BY championed_at DESC');
		$moves_sql = $this->pdo->query('SELECT * FROM moves ORDER BY pokemon_id DESC');

		$pokemons = $pokemons_sql->fetchAll(PDO::FETCH_ASSOC);
		$games_result = $games_sql->fetchAll(PDO::FETCH_ASSOC);
		$moves = $moves_sql->fetchAll(PDO::FETCH_ASSOC);

		foreach ($moves as $move) {
			$move_map[$move['pokemon_id']][] = $move;
		}

		foreach ($pokemons as $pokemon) {
			$currentPokemonMoves = $move_map[$pokemon['id']] ?? [];
			$pokemon['sprite'] = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/' . ($pokemon['poke_id'] ?? '0') . '.png';
			$pokemon['image'] = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/home/' . ($pokemon['poke_id'] ?? '0') . '.png';
			$pokemon_map[$pokemon['game_id']][] = array_merge($pokemon, ['moves' => $currentPokemonMoves]);
		}
		foreach ($games_result as $game) {
			$currentGamePokemons = $pokemon_map[$game['id']] ?? [];
			$games[] = array_merge($game, ['pokemons' => $currentGamePokemons]);
		}

		return $games;
	}
}
