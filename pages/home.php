<?php

$title = 'Inicio | Poke Fame';
$description = 'Pagina principal de Poke Fame.';

$conn = Database::getInstance()->getConnection();
$stmt = $conn->prepare('
	SELECT 
		p.*, 
		g.game_key,
		g.region,
		g.player_name,
		g.game_name
	FROM pokemons p
	JOIN games g ON p.game_id = g.id
	
	ORDER BY p.level DESC
');
$stmt->execute();
$pokemons = $stmt->fetchAll(PDO::FETCH_ASSOC);

debug($pokemons);
?>

<h1>Bienvenido a Poke Fame</h1>

<div class="pokemons">
	<?php foreach ($pokemons as $pokemon): ?>
		<div class="pokemon-card">
			<h2><?= $pokemon['nickname']; ?></h2>
			<small><?= $pokemon['species']; ?></small>
			<small><?= $pokemon['name']; ?></small>
			<p><strong>Juego:</strong> <?= $pokemon['game_name']; ?></p>
			<p><strong>Nivel:</strong> <?= $pokemon['level']; ?></p>
		</div>
	<?php endforeach; ?>
</div>

