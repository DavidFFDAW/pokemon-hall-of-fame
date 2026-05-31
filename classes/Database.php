<?php

final class Database
{
	private static ?PDO $connection = null;
	
    private function __construct() {}
	
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
        	self::$connection = new PDO(
				'mysql:host=localhost;dbname=poke-hof',
				'root',
				'',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'
                ]
            );
            self::migrate();
        }

        return self::$connection;
    }

    private static function migrate(): void
    {
        $sql = file_get_contents(STORAGE_PATH . '/migrations.sql');
        self::$connection->exec($sql);
    }
}