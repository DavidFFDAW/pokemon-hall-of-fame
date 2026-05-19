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
}
