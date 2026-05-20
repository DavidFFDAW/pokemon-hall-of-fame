<?php

class Database
{
    protected \PDO $pdo;
    protected static ?self $instance = null;

    private function __construct()
    {
        // sqlite with pdo
        $this->pdo = new PDO('sqlite:' . STORAGE_PATH . '/hall_of_fame.sqlite');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('PRAGMA encoding = "UTF-8";');
        $this->pdo->exec('PRAGMA foreign_keys = ON;');

        // create tables if not exists
        $this->migrate();
    }

    private function migrate(): void
    {
        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS games (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                game_key VARCHAR(150),
                game_name VARCHAR(200),
                player_name VARCHAR(20),
				region VARCHAR(20),
                created_at DATETIME,
                championed_at DATETIME
            );
        ');

        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS pokemons (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                poke_id INTEGER NOT NULL,
                name TEXT NOT NULL,
                nickname TEXT NOT NULL,
                species TEXT NOT NULL,
                types TEXT NOT NULL,
                level INTEGER NOT NULL,
                game_id INTEGER NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
            );
        ');

		// create table for moves
		$this->pdo->exec('
			CREATE TABLE IF NOT EXISTS moves (
				name TEXT NOT NULL,
				og_name TEXT NOT NULL,
				type TEXT NOT NULL,
				power INTEGER NOT NULL,
				accuracy INTEGER NOT NULL,
				pp INTEGER NOT NULL,
				effect TEXT NOT NULL,
				pokemon_id INTEGER NOT NULL,
				created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
				FOREIGN KEY (pokemon_id) REFERENCES pokemons(id) ON DELETE CASCADE
			);
		');
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
