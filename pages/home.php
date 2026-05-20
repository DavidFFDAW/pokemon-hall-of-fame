<?php

$title = 'Inicio | Poke Fame';
$description = 'Pagina principal de Poke Fame.';

$database = Database::getInstance();
$games = $database->getHomeRequest();

// debug($games);
?>

<h1>Bienvenido a Poke Fame</h1>

<div class="pokemons">
	<?php foreach ($games as $game): ?>
		<div class="game-card">
			<h2><?= $game['game_name']; ?></h2>
			<small><?= $game['player_name']; ?></small>
			<small><?= $game['region']; ?></small>
			<?php foreach ($game['pokemons'] as $pokemon): ?>
				<div class="pokemon-card">
					<img src="<?= $pokemon['image']; ?>" alt="<?= $pokemon['name']; ?>" class="pokemon-sprite" />
					<div class="pokemon-info">
						<h3><?= $pokemon['nickname']; ?> (<?= $pokemon['name']; ?>)</h3>
						<p>Nivel: <?= $pokemon['level']; ?></p>
					</div>

					<?php if (!empty($pokemon['moves'])): ?>
						<div class="pokemon-moves">
							<?php foreach ($pokemon['moves'] as $move): ?>
								<div class="move">
									<img width="20" src="https://raw.githubusercontent.com/duiker101/pokemon-type-svg-icons/refs/heads/master/icons/<?= $move['type'] ?? 'normal' ?>.svg" alt="<?= $move['type'] ?? 'normal' ?>" class="move-type-icon type type-<?= $move['type'] ?? 'normal' ?>" />
									<span class="move-name"><?= $move['name'] ?? 'Unknown' ?></span>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
		</div>
	<?php endforeach; ?>
</div>
<?php endforeach; ?>
</div>