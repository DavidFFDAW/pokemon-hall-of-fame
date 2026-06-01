<?php

class PokeApi
{
    private static $baseUrl = 'https://pokeapi.co/api/v2/';

    public static function getGames()
    {
        $url = self::$baseUrl . 'version?limit=10000';
        $games = self::fetchData($url)['results'] ?? [];

        return array_map(function ($game) {
            return $game['name'] ?? 'Unknown';
        }, $games);
    }

    public static function getRegions()
    {
        $url = self::$baseUrl . 'region?limit=10000';
        $regions = self::fetchData($url)['results'] ?? [];

        return array_map(function ($region) {
            return $region['name'] ?? 'Unknown';
        }, $regions);
    }

    public static function getPokemons(): array
    {
        $pokemons = [];
        $url = self::$baseUrl . 'pokemon?limit=100000';
        $response = self::fetchData($url)['results'] ?? [];

        foreach ($response as $pokemon) {
            $pokeID = basename($pokemon['url'] ?? '');
            $pokemons[$pokeID] = [
                'id' => $pokeID,
                'name' => $pokemon['name'] ?? 'Unknown',
                'sprite' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/' . ($pokeID ?? '0') . '.png',
                'image' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/home/' . ($pokeID ?? '0') . '.png',
                'url' => $pokemon['url'] ?? null
            ];
        }

        return $pokemons;
    }

    public static function getPokemonById(string $id): ?array
    {
        $url = self::$baseUrl . 'pokemon/' . $id;
        $pokemon = self::fetchData($url);
        if (!$pokemon) return null;
        return $pokemon;
    }

    public static function getVersionData(string $version): ?array
    {
        $url = self::$baseUrl . 'version/' . $version;
        $versionData = self::fetchData($url);
        if (!$versionData) return null;
        return $versionData;
    }

    public static function getPokemonMoves(string $id): ?array
    {
        $moves = [];
        $url = self::$baseUrl . 'pokemon/' . $id;
        $pokemon = self::fetchData($url);
        if (!$pokemon) return [];
        return $pokemon['moves'] ?? [];
    }

    public static function getLanguageFromLanguagesArray(array $languages, string $targetLanguage = 'es', string $field = 'name'): string
    {
        foreach ($languages as $language) {
            if (($language['language']['name'] ?? '') === $targetLanguage) {
                return $language[$field] ?? 'unknown';
            }
        }
        return 'unknown';
    }

    public static function getNatures(): array
    {
        return array(
            'hardy' => 'Fuerte',
            'bold' => 'Osada',
            'modest' => 'Modesta',
            'calm' => 'Serena',
            'timid' => 'Miedosa',
            'lonely' => 'Huraña',
            'docile' => 'Dócil',
            'mild' => 'Afable',
            'gentle' => 'Amable',
            'hasty' => 'Activa',
            'adamant' => 'Firme',
            'impish' => 'Agitada',
            'bashful' => 'Tímida',
            'careful' => 'Cauta',
            'rash' => 'Alocada',
            'jolly' => 'Alegre',
            'naughty' => 'Pícara',
            'lax' => 'Floja',
            'quirky' => 'Rara',
            'naive' => 'Ingenua',
            'brave' => 'Audaz',
            'relaxed' => 'Plácida',
            'quiet' => 'Mansa',
            'sassy' => 'Grosera',
            'serious' => 'Seria',
        );
    }

    public static function getApiData(string $endpoint, bool $fullURL = false)
    {
        $url = $fullURL ? $endpoint : self::$baseUrl . trim($endpoint, '/');
        $data = self::fetchData($url);
        if (!$data) return null;
        return $data;
    }

    public static function fetch(string $endpoint, bool $fullURL = false)
    {
        return self::getApiData($endpoint, $fullURL);
    }

    private static function fetchData(string $url)
    {
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
