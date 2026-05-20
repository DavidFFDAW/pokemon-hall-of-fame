<?php
$db = Database::getInstance();
$pokemonManager = new Pokemon($db);
$moveManager = new Moves($db);
$pokemon_id = $_GET['pokemon_id'] ?? null;
if (!$pokemon_id) {
	Flash::add('ID de Pokémon no proporcionado', 'error');
	redirect('/admin/pokemons');
}

if (is_post_request()) {
	$moveManager->upsert([
		'pokemon_id' => $pokemon_id,
		'moves' => $_POST['selected_moves'] ?? [],
	]);
	redirect('/admin/pokemons');
}


$stored_pokemon = $pokemonManager->getPokemonByIdWithGame($pokemon_id);
if (!$stored_pokemon) {
	Flash::add('Pokemon no encontrado', 'error');
	redirect('/admin/pokemons');
}
$gameID = $stored_pokemon['game_key'] ?? null;
$pokemonRealID = $stored_pokemon['poke_id'] ?? null;
if (!$pokemonRealID || !$gameID) {
	Flash::add('El Pokémon no tiene un ID válido o no pertenece a un juego válido', 'error');
	redirect('/admin/pokemons');
}
$version = PokeApi::getVersionData($gameID);
$versionName = $version['version_group']['name'] ?? null;
$moves = $moveManager->getMovesForPokemonVersion($pokemonRealID, $versionName);
$pokemonName = $stored_pokemon['name'] ?? 'Unknown';
$storedMoves = $moveManager->getStoredMoveNamesForPokemon($pokemon_id);
?>

<style>
	.moves {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
		gap: 10px;
	}

	.moves .move {
		display: flex;
		justify-content: flex-start;
		align-items: center;
		gap: 10px;
		border: 1px solid #ccc;
		border-radius: 50px;
		min-width: 150px;
		box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
		overflow: hidden;
	}

	.moves .move span {
		width: 100%;
		padding: 6px 4px;
		text-align: center;
		font-weight: 600;
	}

	.moves .move .image-container {
		height: 100%;
		position: relative;
		padding: 0 10px;
	}

	.moves .move .move-type-icon {
		display: block;
		width: 40px;
		height: 100%;
		padding: 4px 12px;
		object-fit: contain;
		z-index: 1;
	}
</style>

<h1>Movimientos de <?= $pokemonName; ?> para <?= $versionName; ?></h1>

<form action="" method="post">
	<input type="hidden" name="pokemon_id" value="<?= $pokemon_id; ?>" />

	<div class="moves">
		<?php foreach ($moves as $en => $move): ?>
			<label class="label radio-label">
				<input type="checkbox" name="selected_moves[]" data-es="<?= $move['name'] ?? ''; ?>" value="<?= $en ?? ''; ?>" onchange="handleChangeMove(event)"
					<?= in_array($en, $storedMoves) ? 'checked' : ''; ?>
				/>
				<div class="move label">
					<img src="https://raw.githubusercontent.com/duiker101/pokemon-type-svg-icons/refs/heads/master/icons/<?= $move['type'] ?? 'normal' ?>.svg" alt="<?= $move['type'] ?? 'normal' ?>" class="move-type-icon type type-<?= $move['type'] ?? 'normal' ?>" />
					<span class="move-name"><?= $move['name'] ?? 'Unknown' ?></span>
				</div>
			</label>
		<?php endforeach; ?>
	</div>
	<button type="submit">Guardar movimientos</button>
</form>

<script>
	const moves = <?= json_encode($moves); ?>;
	console.log({
		moves
	});


	function handleChangeMove(event) {
		const checks = document.querySelectorAll('input[name="selected_moves[]"]:checked');
		console.log({ checks });
		
		if (checks.length > 4) {
			event.target.checked = false;
		}
	}
</script>