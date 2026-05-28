<?php
$id = $_GET['id'] ?? null;
$leaguesModel = new Leagues();
$pokemonsModel = new Pokemons();
$leagues = $leaguesModel->getLeagues();
// debug($leagues);
$pokemons = PokeApi::getPokemons();

$savedPokemon = null;
if ($id) $savedPokemon = $pokemonsModel->find($id);
$isUpdate = boolval($id) && $id !== 0 && ($savedPokemon['id'] ?? null);

// if (is_post_request()) {}
?>

<style>
	body main {
		padding: 0 20px;
	}
	.box {
		max-height: calc(100dvh - 54px);
		min-height: calc(100dvh - 54px);
		border-radius: 0;
	}
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
	.pokemon-container .pokemon-list-container label .label span {
		font-family: 'floral', sans-serif;
		font-weight: 400;
		font-size: 0.9em;
	}
	.pokemon-container .pokemon-list-container label img {
		width: 60px;
		height: auto;
		object-fit: contain;
	}
	
	.pokemon-gender-container {
		display: flex;
		gap: 15px;
	}

</style>

<div class="box">
	<h1 class="panel-title"><?= $savedPokemon ? 'Editar pokemon' : 'Agregar nuevo pokemon'; ?></h1>

	<form action="" method="POST" class="pokemon-upsert-step-checkout">
		<input type="hidden" name="id" value="<?= $savedPokemon['id'] ?? ''; ?>" />
		<input type="hidden" name="action" value="<?= $isUpdate ? 'update' : 'create'; ?>" />
		<input type="hidden" name="update_id" value="<?= $savedPokemon['id'] ?? ''; ?>" />
		<input type="hidden" id="step" name="step" value="1" />

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
							onchange="handlePokemonChange(event, this)"
						/>
						<div class="label">
							<img
								src="<?= $pokemon['image']; ?>" 
								alt="<?= $pokemon['name']; ?>" 
								loading="lazy" decoding="async" 
								draggable="false" 
							/>
							<span>
								<?= $pokemon['name']; ?>
							</span>
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

			<label class="label">
                <span class="label">Item</span>
                <input type="text" id="item" name="item" value="<?= $savedPokemon['item'] ?? ''; ?>">
            </label>

			<label class="label">
                <span class="label">Habilidad</span>
				<input type="text" id="ability" name="ability" value="<?= $savedPokemon['ability'] ?? ''; ?>">
            </label>

			<label class="label">
                <span class="label">Genus</span>
				<input type="text" id="genus" name="genus" value="<?= $savedPokemon['genus'] ?? ''; ?>">
            </label>

			<div class="pokemon-gender-container">
				<label class="label radio-label">
					<input type="radio" name="gender" value="m"/>
					<div class="label">
						<i class="bi bi-gender-male"></i>
						<span>Masculino</span>
					</div>
				</label>
				<label class="label radio-label">
					<input type="radio" name="gender" value="f"/>
					<div class="label">
						<i class="bi bi-gender-female"></i>
						<span>Femenino</span>
					</div>
				</label>
			</div>
		</div>

		<button type="submit" class="btn"><?= $isUpdate ? 'Actualizar' : 'Crear'; ?></button>
	</form>
</div>

<script type="text/javascript">
	const isUpdate = Boolean(<?= $isUpdate ? 1 : 0; ?>);
	let currentStep = isUpdate ? 3 : 1;

	const leagueId = <?= json_encode($_GET['league_id'] ?? null); ?>;
	const savedPokemon = <?= json_encode($savedPokemon ?? null); ?>;
	const $pokemonLabels = Array.from(document.querySelectorAll(`.pokemon-list-container label`)).map(label => {
		return {
			element: label,
			name: label.getAttribute('data-pokemon-name')
		}
	});

	function handlePokemonChange(event, input) {
		if (input.checked) {
			const selectedLabel = input.closest('label');
			const pokemonName = selectedLabel.getAttribute('data-pokemon-name');
			scrollToPokemonByName(pokemonName);
		}
	}


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
