<?php
$id = $_GET['id'] ?? null;
if (!isset($_GET['game_id'])) {
	Flash::add('ID de juego no proporcionado para asignar al pokémon.', 'error');
	redirect('/admin/pokemons');
}
$pokemons = PokeApi::getPokemons();
$database = Database::getInstance();
$pokemonsModel = new Pokemon($database);
$pokemonsInGame = $pokemonsModel->getHowManyPokemonsByGameId($_GET['game_id']);
if ($pokemonsInGame >= 6) {
	Flash::add('El juego seleccionado ya tiene 6 pokémons asignados. No se pueden asignar más.', 'error');
	redirect('/admin/pokemons');
}

$savedPokemon = null;
if ($id) $savedPokemon = $pokemonsModel->getPokemonById($id);
$isUpdate = boolval($id) && $id !== 0 && ($savedPokemon['id'] ?? null);

if (is_post_request()) {
	$upserted = $pokemonsModel->upsert(
		$_POST,
		$savedPokemon
	);
	if ($upserted) {
		Flash::add('Pokémon guardado exitosamente.', 'success');
		redirect('/admin/pokemons');
	}
}
?>

<style>
	.game-version-container {
		display: flex;
		flex-wrap: wrap;
		gap: 10px;
	}

	.radio-label .label {
		display: block;
		text-align: center;
		text-transform: capitalize;
	}

	.upsert-datas-container {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		padding: 50px 0 0 0;
		gap: 10px;
	}

	.upsert-datas-container label.label {
		margin: 0;
	}


	.pokemon-container {
		display: flex;
		flex-direction: column;
		border: 1px solid #ccc;
		border-radius: 8px;
		overflow: hidden;
		gap: 0;
	}
	.pokemon-container .pokemon-searcher input {
		width: 100%;
		padding: 8px;
		border: none;
		border-bottom: 1px solid #ccc;
		border-radius: 8px 8px 0 0;
	}
	.pokemon-container .pokemon-list-container {
		max-width: 100%;
		display: flex;
		padding: 10px;
		overflow-y: hidden;
		overflow-x: auto;
		gap: 10px;
	}
	.pokemon-container .pokemon-list-container label {
		flex: 0 0 auto;
		min-width: 100px;
	}
	.pokemon-container .pokemon-list-container label .label {
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 5px;
		text-transform: capitalize;
	}
	.pokemon-container .pokemon-list-container label img {
		width: 60px;
		height: auto;
		object-fit: contain;
	}

</style>

<div class="box">
	<h2><?= $savedPokemon ? 'Editar pokemon' : 'Agregar nuevo pokemon'; ?></h2>

	<form action="" method="POST">
		<input type="hidden" name="id" value="<?= $savedPokemon['id'] ?? ''; ?>" />
		<input type="hidden" name="action" value="<?= $isUpdate ? 'update' : 'create'; ?>" />
		<input type="hidden" name="game_id" id="game_id" value="<?= $savedPokemon['game_id'] ?? $_GET['game_id'] ?? ''; ?>" />

		<div class="pokemon-container">
			<div class="pokemon-searcher">
				<input type="text" oninput="searchPokemons(event, this)" placeholder="Buscar Pokémon..." />
			</div>
			<div class="pokemon-list-container">
				<?php foreach ($pokemons as $pokemon): ?>
					<label class="label radio-label" data-pokemon-name="<?= strtolower($pokemon['name']); ?>">
						<input type="radio"
							name="pokemon_id"
							value="<?= $pokemon['id']; ?>"
							<?= (isset($savedPokemon['name']) && $savedPokemon['name'] === $pokemon['name']) ? 'checked' : ''; ?>
							onchange="handleGameVersionChange(event, this)"
						/>
						<div class="label">
							<img
								src="<?= $pokemon['image']; ?>" 
								alt="<?= $pokemon['name']; ?>" 
								loading="lazy" decoding="async" 
								draggable="false" 
							/>
							<?= $pokemon['name']; ?>
						</div>
					</label>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="upsert-datas-container">
			<label class="label">
                <span class="label">Mote</span>
                <input type="text" id="nickname" name="nickname" value="<?= $savedPokemon['nickname'] ?? ''; ?>">
            </label>

			<label class="label">
                <span class="label">Nivel</span>
                <input type="number" id="level" name="level" value="<?= $savedPokemon['level'] ?? ''; ?>">
            </label>
		</div>

		

		<button type="submit" class="btn"><?= $isUpdate ? 'Actualizar' : 'Crear'; ?></button>
	</form>
</div>

<script type="text/javascript">
	const gameId = <?= json_encode($_GET['game_id'] ?? null); ?>;
	const savedPokemon = <?= json_encode($savedPokemon ?? null); ?>;
	const $pokemonLabels = Array.from(document.querySelectorAll(`.pokemon-list-container label`)).map(label => {
		return {
			element: label,
			name: label.getAttribute('data-pokemon-name')
		}
	});


	function scrollToPokemonByName(name) {
		// const $pokemonLabel = document.querySelector(`.pokemon-list-container label[data-pokemon-name="${name.toLowerCase()}"]`);
		// if ($pokemonLabel) $pokemonLabel.scrollIntoView({ behavior: 'smooth', block: 'start' });
		const pokemonData = $pokemonLabels.find(label => label.name.includes(name));
		if (pokemonData) pokemonData.element.scrollIntoView({ behavior: 'smooth', block: 'start' });
	}

	function searchPokemons(event, input) {
		event.preventDefault();
		const searchTerm = input.value.toLowerCase();
		scrollToPokemonByName(searchTerm);
	}

	window.onload = function() {
		if (savedPokemon) scrollToPokemonByName(savedPokemon.name);
	}
</script>
