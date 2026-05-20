<?php

class PokeApi {
    private static $baseUrl = 'https://pokeapi.co/api/v2/';

    public static function getGames() {
        $url = self::$baseUrl . 'version?limit=10000';
        $games = self::fetchData($url)['results'] ?? [];
		
		return array_map(function ($game) {
			return $game['name'] ?? 'Unknown';
		}, $games);
    }

	public static function getRegions() {
		$url = self::$baseUrl . 'region?limit=10000';
		$regions = self::fetchData($url)['results'] ?? [];
		
		return array_map(function ($region) {
			return $region['name'] ?? 'Unknown';
		}, $regions);
	}

	public static function getPokemons(): array {
		$pokemons = [];
		$url = self::$baseUrl . 'pokemon?limit=100000';
		$response = self::fetchData($url)['results'] ?? [];
		
		foreach ($response as $pokemon) {
			$pokeID = basename($pokemon['url'] ?? '');
			$pokemons[] = [
				'id' => $pokeID,
				'name' => $pokemon['name'] ?? 'Unknown',
				'sprite' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/' . ($pokeID ?? '0') . '.png',
				'image' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/home/' . ($pokeID ?? '0') . '.png',
				'url' => $pokemon['url'] ?? null
			];
		}

		return $pokemons;
	}

	public static function getPokemonById(string $id): ?array {
		$url = self::$baseUrl . 'pokemon/' . $id;
		$pokemon = self::fetchData($url);
		if (!$pokemon) return null;
		return $pokemon;
	}

	public static function getVersionData(string $version): ?array {
		$url = self::$baseUrl . 'version/' . $version;
		$versionData = self::fetchData($url);
		if (!$versionData) return null;
		return $versionData;
	}

	public static function getPokemonMoves(string $id): ?array {
		$moves = [];
		$url = self::$baseUrl . 'pokemon/' . $id;
		$pokemon = self::fetchData($url);
		if (!$pokemon) return [];
		return $pokemon['moves'] ?? [];
	}

    private static function fetchData(string $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        if (curl_errno($ch)) return null;

        curl_close($ch);
        return json_decode($response, true);
    }
}