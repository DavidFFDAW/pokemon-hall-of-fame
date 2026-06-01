<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if (!isset($_GET['id']))
    response('ID is required', 400);

$pokemonId = $_GET['id'];
$pokemonData = PokeApi::getPokemonById($pokemonId);
unset($pokemonData['moves']);
$abilities = [];

foreach ($pokemonData['abilities'] ?? [] as $ability) {
    $abilityInfo = PokeApi::getApiData($ability['ability']['url'] ?? '', true);
    $esname = PokeApi::getLanguageFromLanguagesArray($abilityInfo['names'] ?? [], 'es');
    $abilities[] = $esname;
}

response('Pokemon data retrieved successfully', 200, [
    'id' => $pokemonData['id'] ?? null,
    'name' => $pokemonData['name'] ?? null,
    'abilities' => $abilities
]);
