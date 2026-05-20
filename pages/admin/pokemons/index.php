<?php
$database = Database::getInstance();
$pokemons = $database->getGamesWithPokemons();
?>

<style>
	.games-container {
		display: flex;
		flex-wrap: wrap;
		gap: 20px;
	}

	.games-container .game {
		flex: 1 1 300px;
		background-color: #fff;
		border: 1px solid #ccc;
		border-radius: 8px;
		padding: 15px;
		box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
	}

	.games-container .game .game-pokemons-container {
		display: flex;
		max-width: 100%;
		overflow-x: auto;
		overflow-y: hidden;
		margin-top: 10px;
		gap: 10px;
	}

	.games-container .game .game-pokemons-container .pokemon-card {
		flex: 0 0 auto;
		display: flex;
		flex-direction: column;
		align-items: center;

		position: relative;
		border: 1px solid #eee;
		border-radius: 8px;
		padding: 10px;
		background-color: #f9f9f9;
		box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
		overflow: hidden;
	}

	.games-container .game .game-pokemons-container .pokemon-card a.delete {
		position: absolute;
		top: 5px;
		right: 5px;
		background-color: rgba(255, 0, 0, 0.8);
		color: #fff;
		border: none;
		border-radius: 50%;
		width: 24px;
		height: 24px;
		display: flex;
		align-items: center;
		justify-content: center;
		text-decoration: none;
		font-size: 14px;
		font-weight: bold;
	}

	.games-container .game .game-pokemons-container img {
		width: 140px;
		height: 140px;
		object-fit: contain;
	}

	.games-container .game .game-pokemons-container .actions-container {
		width: 100%;
		display: flex;
		gap: 5px;
		margin-top: 10px;
	}

	.games-container .game .game-pokemons-container .actions-container>a {
		width: 100%;
		display: block;
		padding: 6px 0;
		text-align: center;
		background-color: #fff;
		color: #333;
		border: 1px solid #ccc;
		border-radius: 4px;
		text-decoration: none;
		font-size: 14px;
	}
</style>

<div class="games-container">
	<?php foreach ($pokemons as $game): ?>
		<div class="game game-box game-card">
			<h2><?= $game['game_name']; ?></h2>
			<small><?= $game['player_name']; ?></small>
			<small><?= $game['region']; ?></small>

			<div class="pokemons game-pokemons-container">
				<?php foreach ($game['pokemons'] as $pokemon): ?>
					<div class="pokemon-card">
						<img src="<?= $pokemon['image']; ?>" alt="<?= $pokemon['name']; ?>" class="pokemon-sprite" />
						<div class="pokemon-info">
							<h3><?= $pokemon['nickname']; ?></h3>
							<p>Nivel: <?= $pokemon['level']; ?></p>
						</div>
						<div class="actions-container">
							<a href="<?= PATHNAME; ?>/delete?id=<?= $pokemon['id']; ?>" class="delete" aria-label="Eliminar pokémon" onclick="return confirm('¿Estás seguro de que deseas eliminar este pokémon?')">×</a>
							<a href="<?= PATHNAME; ?>/upsert?id=<?= $pokemon['id']; ?>&game_id=<?= $game['id']; ?>" class="edit" aria-label="Editar pokémon">✎</a>
							<a href="<?= PATHNAME; ?>/moves?pokemon_id=<?= $pokemon['id']; ?>&game_id=<?= $game['id']; ?>" class="edit" aria-label="Editar movimientos">⚡</a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endforeach; ?>


	<a href="<?= PATHNAME; ?>/upsert" class="btn create-button" aria-label="Agregar nuevo pokémon">Agregar nuevo pokémon</a>
</div>