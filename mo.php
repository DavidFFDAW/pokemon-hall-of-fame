<?php

// $directory = dirname(__FILE__) . DIRECTORY_SEPARATOR;
// require_once $directory . 'classes/PokeApi.php';
// require_once $directory . 'functions.php';

// $tr = file_exists($directory . 'moves.json')
// 	? json_decode(file_get_contents($directory . 'moves.json'), true)
// 	: [];
// $savedMoves = array_keys($tr);

// $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
// $limit = 100;
// $offset = ($page - 1) * $limit;

// $moves = PokeApi::getApiData("move?limit=$limit&offset=$offset");

// $apiMoves = array();

// foreach ($moves['results'] as $move) {
// 	$moveName = $move['name'];
// 	$apiMoves[$moveName] = array(
// 		'id' => rtrim(str_replace('https://pokeapi.co/api/v2/move/', '', $move['url']), '/'),
// 		'name' => $moveName,
// 		'url' => $move['url'],
// 	);
// }

// $moveNames = array_keys($apiMoves);
// $nonRepeated = array_diff($moveNames, $savedMoves);
// if (empty($nonRepeated)) {
// 	echo "No hay movimientos nuevos en esta página.";
// 	echo "\nMovimientos en la API: " . count($moveNames);
// 	echo "\nMovimientos guardados: " . count($savedMoves);
// 	echo "<a href=''>Siguiente</a>";
// 	exit;
// }

// foreach ($nonRepeated as $moveName) {
// 	if (!isset($apiMoves[$moveName])) continue;

// 	$move = $apiMoves[$moveName];
// 	$moveData = PokeApi::getApiData($move['url'], true);
// 	if (!$moveData || empty($moveData)) continue;

// 	$esNames = array_filter($moveData['names'], function ($name) {
// 		return $name['language']['name'] === 'es';
// 	});
// 	$esFlavors = array_filter($moveData['flavor_text_entries'], function ($entry) {
// 		return $entry['language']['name'] === 'es';
// 	});
// 	$last = array_key_first($esFlavors);
// 	$esFlavor = $esFlavors[$last]['flavor_text'] ?? '';
// 	$esName = $esNames ? array_values($esNames)[0]['name'] : $moveName;

// 	$apiMoves[$moveName]['effect'] = $esFlavor;
// 	$apiMoves[$moveName]['name'] = $esName;
// 	$apiMoves[$moveName]['type'] = $moveData['type']['name'] ?? '--';
// 	$apiMoves[$moveName]['power'] = $moveData['power'] ?? '--';
// 	$apiMoves[$moveName]['accuracy'] = $moveData['accuracy'] ?? '--';
// 	$apiMoves[$moveName]['pp'] = $moveData['pp'] ?? '--';
// 	unset($apiMoves[$moveName]['url']);
// }

// file_put_contents($directory . 'moves.json', json_encode($apiMoves, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));