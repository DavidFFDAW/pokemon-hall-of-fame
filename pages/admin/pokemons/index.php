<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$league = new Leagues();
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || empty($_GET['id']))
	redirect('/admin/leagues');

$league = $league->getLeague((int) $_GET['id']);
// $pokemons = new Pokemons();
// $list = $pokemons->get('WHERE game_id = ' . $game['game_id'] . ' ORDER BY level DESC');
$apimons = PokeApi::getPokemons();

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

	.slider {
		aspect-ratio: 16 / 9;
		width: 300px;
		position: relative;
		display: flex;
		overflow: scroll;
		scroll-snap-type: x mandatory;
	}
	.slider > img {
		width: 100%;
		position: sticky;
		left: 0;
		scroll-snap-align: center;
	}
</style>

<div class="games-container">
	<div class="slider">
		<?php foreach (array_slice($apimons, 0, 50) as $pokemon): ?>
			<img 
				draggable="false"
				class="pokemon-card"
				src="<?= $pokemon['image'] ?>"
				alt="<?= e($pokemon['name']) ?>"
			/>
		<?php endforeach; ?>
	</div>

	<a href="<?= PATHNAME; ?>/upsert" class="btn create-button" aria-label="Agregar nuevo pokémon">Agregar nuevo pokémon</a>
</div>