<?php
$id = $_GET['id'] ?? null;
$leaguesModel = new Leagues();
$pokemonsModel = new Pokemons();
$leagues = $leaguesModel->getLeagues();
$pokemons = PokeApi::getPokemons();
$natures = PokeApi::getNatures();
// $pokemons = [];

$savedPokemon = null;
if ($id) $savedPokemon = $pokemonsModel->find($id);
$isUpdate = boolval($id) && $id !== 0 && ($savedPokemon['id'] ?? null);
$currentStep = $isUpdate ? 3 : 1;
$lastStep = 3;

$isCurrentPokemonInitial = false;
$initialRenderPokemons = array_slice($pokemons, 0, 151);
$currentPokemon = isset($savedPokemon['api_id']) ? $pokemons[$savedPokemon['api_id']] ?? false : false;
if ($currentPokemon) {
    $initialIds = array_column($initialRenderPokemons, 'id');
    $isCurrentPokemonInitial = in_array($currentPokemon['id'] ?? null, $initialIds);
}

if (is_post_request()) {
    debug($_POST);
}
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
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 10px;
    }

    .upsert-datas-container label.label {
        margin: 0;
    }

    .upsert-datas-container .pokemon-gender-container label.label {
        flex: 1;
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
        gap: 5px;
    }

    form.pokemon-upsert-step-checkout {
        position: relative;
        min-height: calc(100dvh - 54px);
        max-height: calc(100dvh - 54px);
        background-color: #fff;
        border: 1px solid #ccc;
        border-top: none;
        border-bottom: none;
        padding: 20px 15px;
        padding-bottom: 80px;
        overflow-y: auto;
    }

    form .form-steps-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-step-button-container {
        position: fixed;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        max-width: calc(800px - 60px);
        border-radius: 12px 12px 0 0;
        border: 1px solid #ccc;
        border-bottom: none;
        margin: 0 auto;
        padding: 10px;
        background-color: #fff;
        border-top: 1px solid #ccc;

        display: flex;
        justify-content: space-between;
    }

    .form-step-button-container button {
        padding: 8px 15px;
        border: none;
        background-color: #007bff;
        color: #fff;
        border-radius: 5px;
        cursor: pointer;
    }

    .form-step-button-container button[type="submit"] {
        align-self: flex-end;
    }


    form.pokemon-upsert-step-checkout .form-step {
        display: none;
        flex-direction: column;
        gap: 20px;
    }

    form.pokemon-upsert-step-checkout .form-step header.form-step-header {
        display: flex;
        position: relative;
        flex-direction: column;
        justify-content: center;
        gap: 5px;
    }

    form.pokemon-upsert-step-checkout .form-step header.form-step-header>.form-step-title {
        width: 100%;
        position: relative;
        text-align: center;
        margin: 0;
        font-family: 'floral', sans-serif;
        font-weight: 400;
        font-size: 1.5em;
    }

    form.pokemon-upsert-step-checkout .form-step header.form-step-header>.form-step-title::after {
        content: '';
        position: absolute;
        bottom: -3px;
        left: 50%;
        transform: translateX(-50%);
        width: 10%;
        height: 4px;
        background-color: #007bff;
        border-radius: 50px;
    }

    form.pokemon-upsert-step-checkout .form-step header.form-step-header>p {
        width: 100%;
        text-align: center;
        font-size: 0.9em;
        color: #666;
    }

    form.pokemon-upsert-step-checkout .form-step-button-container .btn.back-button {
        background-color: transparent;
        border: 1px solid #007bff;
        color: #007bff;
    }


    form.pokemon-upsert-step-checkout[data-current-step="1"] .form-step-1,
    form.pokemon-upsert-step-checkout[data-current-step="2"] .form-step-2,
    form.pokemon-upsert-step-checkout[data-current-step="3"] .form-step-3 {
        display: flex;
    }

    .grid.list-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
    }

    .game-version-image {
        width: 100%;
        max-width: 100%;
        height: auto;
        max-height: 200px;
        object-fit: cover;
    }

    .pokemon-list-container img {
        width: 120px;
        height: 120px;
        object-fit: contain;
    }

    .search-pokemon-container {
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .search-pokemon-container input {
        width: 100%;
        max-width: 400px;
        padding: 6px 12px;
        border: 1px solid #ccc;
        border-radius: 12px;
    }

    form.pokemon-upsert-step-checkout .pokemon-list-container label.label.radio-label input[type="radio"]+.label,
    form.pokemon-upsert-step-checkout .pokemon-list-container label.label.radio-label input[type="checkbox"]+.label {
        height: 100%;
    }

    form.pokemon-upsert-step-checkout .pokemon-list-container {
        max-height: calc(100dvh - 54px - 185px);
        border-bottom: 1px solid #ccc;
        padding: 10px 0;
        overflow-y: auto;
        overflow-x: hidden;
    }

    form.pokemon-upsert-step-checkout .pokemon-list-container::-webkit-scrollbar {
        width: 0px;
        height: 0px;
        background: transparent;
    }

    form.pokemon-upsert-step-checkout .pokemon-preview-container {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 12px;
        display: flex;
        justify-content: space-around;
        align-items: center;
        margin-bottom: 20px;
        gap: 10px;
    }
    form.pokemon-upsert-step-checkout .pokemon-preview-container img {
        width: 120px;
        height: 120px;
        object-fit: contain;
    }
    form.pokemon-upsert-step-checkout .pokemon-preview-container span {
        flex: 1;
        display: block;
        font-size: 1.3em;
        text-align: center;
        transform: scaleY(1.2);
        text-transform: capitalize;
        font-family: 'floral', sans-serif;
    }

    form.pokemon-upsert-step-checkout .pokemon-preview-container img.poke-shiny-image,
    form.pokemon-upsert-step-checkout .pokemon-preview-container[data-shiny="true"] img.poke-image {
        display: none;
    }
    form.pokemon-upsert-step-checkout .pokemon-preview-container[data-shiny="true"] img.poke-shiny-image {
        display: block;
    }

    @media only screen and (max-width: 600px) {
        body main {
            padding: 0;
        }

        .form-step-button-container {
            width: 95%;
        }
    }
</style>

<form action="" method="POST" class="pokemon-upsert-step-checkout" id="pokemon-step-form" data-current-step="<?= $currentStep; ?>">
    <input type="hidden" name="id" value="<?= $savedPokemon['id'] ?? ''; ?>" />
    <input type="hidden" name="action" value="<?= $isUpdate ? 'update' : 'create'; ?>" />
    <input type="hidden" name="update_id" value="<?= $savedPokemon['id'] ?? ''; ?>" />

    <div class="form-step form-step-1" data-step="1">
        <header class="form-step-header">
            <h2 class="form-step-title">Liga</h2>
            <p>Selecciona la liga a la que pertenece el Pokémon.</p>
        </header>

        <div class="league-games-list-container grid list-grid">
            <?php foreach ($leagues as $league) : ?>
                <label class="label radio-label">
                    <input type="radio" name="league_id" value="<?= $league['id']; ?>" <?= (isset($savedPokemon['league_id']) && $savedPokemon['league_id'] == $league['id']) ? 'checked' : ''; ?>
                        onchange="setAutoNextStep()" required />
                    <div class="label inner inner-label game-version-container">
                        <img class="game-version-image" src="<?= $league['game_image']; ?>" alt="<?= $league['game_name']; ?> icon" loading="lazy" draggable="false" />
                        <span><?= $league['trainer_name']; ?></span>
                        <small><?= $league['game_name']; ?></small>
                    </div>
                </label>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="form-step form-step-2" data-step="2">
        <header class="form-step-header">
            <h2 class="form-step-title">Pokemon</h2>
            <p>Selecciona el Pokémon que deseas agregar.</p>
            <div class="search-pokemon-container">
                <input type="search" placeholder="Buscar Pokémon por nombre..." onkeydown="if(event.key === 'Enter') searchPokemons(event, this)" onchange="searchPokemons(event, this)" />
            </div>
        </header>

        <div class="pokemon-list-container grid list-grid" id="pokemon-list-container">
            <?php if ($currentPokemon && !$isCurrentPokemonInitial) : ?>
                <label class="label radio-label" data-pokemon-name="<?= $currentPokemon['name']; ?>">
                    <input type="radio" name="pokemon_id" value="<?= $currentPokemon['id']; ?>"
                        onchange="handlePokemonChange(event, this)"
                        checked
                        required />
                    <div class="label inner inner-label game-version-container">
                        <img class="game-version-image" src="<?= $currentPokemon['image']; ?>" alt="<?= $currentPokemon['name']; ?> icon" loading="lazy" draggable="false" />
                        <span><?= $currentPokemon['name']; ?></span>
                    </div>
                </label>
            <?php endif; ?>

            <?php foreach ($initialRenderPokemons as $pokemon) : ?>
                <label class="label radio-label" data-pokemon-name="<?= $pokemon['name']; ?>">
                    <input type="radio" name="pokemon_id" value="<?= $pokemon['id']; ?>" <?= (isset($savedPokemon['api_id']) && $savedPokemon['api_id'] == $pokemon['id']) ? 'checked' : ''; ?>
                        onchange="handlePokemonChange(event, this)"
                        required />
                    <div class="label inner inner-label game-version-container">
                        <img class="game-version-image" src="<?= $pokemon['image']; ?>" alt="<?= $pokemon['name']; ?> icon" loading="lazy" draggable="false" />
                        <span><?= $pokemon['name']; ?></span>
                    </div>
                </label>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="form-step form-step-3" data-step="3">
        <header class="form-step-header">
            <h2 class="form-step-title">Datos</h2>
            <p>Ingresa los datos del Pokémon.</p>
        </header>

        <div class="pokemon-data-container">
            <div class="pokemon-preview-container" id="poke-preview"></div>

            <div class="upsert-datas-container">
                <label class="label">
                    <span class="label">Mote</span>
                    <input type="text" id="nickname" name="nickname" value="<?= $savedPokemon['nickname'] ?? ''; ?>"
                        maxlength="20" required />
                </label>

                <label class="label">
                    <span class="label">Nivel</span>
                    <input type="number" id="level" name="level" value="<?= $savedPokemon['level'] ?? ''; ?>"
                        min="1" max="100" required inputmode="numeric" />
                </label>

                <label class="label">
                    <span class="label">Item</span>
                    <input type="text" id="item" name="item" value="<?= $savedPokemon['item'] ?? ''; ?>"
                        maxlength="50" />
                </label>

                <label class="label">
                    <span class="label">Habilidad</span>
                    <input type="text" id="ability" name="ability" value="<?= $savedPokemon['ability'] ?? ''; ?>" maxlength="50" />
                </label>

                <label class="label">
                    <span class="label">Region</span>
                    <input type="text" id="region" name="region" value="<?= $savedPokemon['region'] ?? ''; ?>" maxlength="50" />
                </label>

                <label class="label">
                    <span class="label">Naturaleza</span>
                    <select id="nature" name="nature" value="<?= $savedPokemon['nature'] ?? ''; ?>">
                        <option value="">-- Selecciona una naturaleza --</option>
                        <?php foreach ($natures as $value) : ?>
                            <option value="<?= $value; ?>" <?= (isset($savedPokemon['nature']) && $savedPokemon['nature'] === $value) ? 'selected' : ''; ?>><?= $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="label">
                    <span class="label">Es shiny</span>
                    <select id="shiny" name="shiny" value="<?= $savedPokemon['shiny'] ?? ''; ?>" onchange="handleShinyChange(event, this)">
                        <option value="0" <?= (isset($savedPokemon['shiny']) && !$savedPokemon['shiny']) ? 'selected' : ''; ?>>No</option>
                        <option value="1" <?= (isset($savedPokemon['shiny']) && $savedPokemon['shiny']) ? 'selected' : ''; ?>>Sí</option>
                    </select>
                </label>

                <div class="pokemon-gender-container">
                    <label class="label radio-label">
                        <input type="radio" name="gender" value="m" <?= (isset($savedPokemon['gender']) && $savedPokemon['gender'] === 'm') ? 'checked' : ''; ?>  required />
                        <div class="label inner gender-inner">
                            <i class="bi bi-gender-male"></i>
                            <span>Masculino</span>
                        </div>
                    </label>
                    <label class="label radio-label">
                        <input type="radio" name="gender" value="f" <?= (isset($savedPokemon['gender']) && $savedPokemon['gender'] === 'f') ? 'checked' : ''; ?>  required />
                        <div class="label inner gender-inner">
                            <i class="bi bi-gender-female"></i>
                            <span>Femenino</span>
                        </div>
                    </label>
                    <label class="label radio-label">
                        <input type="radio" name="gender" value="u" <?= (isset($savedPokemon['gender']) && $savedPokemon['gender'] === 'u') ? 'checked' : ''; ?>  required />
                        <div class="label inner gender-inner">
                            <i class="bi bi-gender-ambiguous"></i>
                            <span>Desconocido</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="form-step-button-container">
        <button
            type="button"
            id="previous-step-button"
            class="btn btn-secondary back-button"
            onclick="previousStep(event)"
            <?= $currentStep === 1 ? 'disabled' : ''; ?>>Atras
        </button>
        <button
            class="btn"
            type="submit"
            onclick="handleStepButtonClick(event, this)">
            <?= $currentStep === $lastStep ? 'Guardar' : 'Siguiente'; ?>
        </button>
    </div>

</form>

<template id="pokemon-radio-item-template">
    <label class="label radio-label">
        <input type="radio" name="pokemon_id" value="{{id}}" required />
        <div class="label inner inner-label game-version-container">
            <img class="game-version-image" src="{{image}}" alt="{{name}} icon" />
            <span>{{name}}</span>
        </div>
    </label>
</template>

<template id="selected-pokemon-template">
    <img class="poke-image" src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/home/[id].png" alt="{{name}} icon" />
    <img class="poke-image poke-shiny-image" src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/home/shiny/[id].png" alt="{{name}} shiny icon" />
    <span>{{name}}</span>
</template>


<!-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> -->
<script type="text/javascript">
    let currentPokemonData = {};
    let currentStep = <?= $currentStep; ?>;
    const lastStep = <?= $lastStep; ?>;
    const isUpdate = Boolean(<?= $isUpdate ? 1 : 0; ?>);
    const pokemonsObject = <?= json_encode(array_values($pokemons)); ?>;
    const pokemons = Object.values(pokemonsObject);

    const pokemonListContainer = document.getElementById('pokemon-list-container');
    const stepForm = document.getElementById('pokemon-step-form');
    const previousStepButton = document.getElementById('previous-step-button');
    const nextStepButton = stepForm.querySelector('button[type="submit"]');

    const pokeRadioItemTemplate = document.getElementById('pokemon-radio-item-template').content;
    const selectedPokemonTemplate = document.getElementById('selected-pokemon-template').content;
    const savedPokemon = <?= json_encode($savedPokemon ?? null); ?>;

    function updateStepForm() {
        stepForm.setAttribute('data-current-step', currentStep);
        previousStepButton.disabled = currentStep === 1;
        nextStepButton.textContent = currentStep === lastStep ? 'Guardar' : 'Siguiente';
    }

    function previousStep(event) {
        if (currentStep > 1) {
            currentStep = currentStep - 1;
            updateStepForm();
        }
    }

    function nextStep(event) {
        if (currentStep < lastStep) {
            currentStep = currentStep + 1;
            updateStepForm();
        }
    }

    function handleStepButtonClick(event, button) {
        if (currentStep < lastStep) {
            event.preventDefault();
            // validate the current step form fields and show error if there are any errors
            const stepFields = stepForm.querySelectorAll(`.form-step-${currentStep} [required]`);
            const valid = Array.from(stepFields).every(field => field.reportValidity());
            if (!valid) return;

            currentStep = currentStep + 1;
            updateStepForm();
        }
    }

    function setAutoNextStep() {
        setTimeout(() => {
            nextStep();
        }, 200);
    }

    function handlePokemonChange(event, element) {
        const pokemonId = element.value;
        const pokemonData = pokemons.find(p => p.id == pokemonId);
        console.log('pokemonData', pokemonData);
        
        currentPokemonData = pokemonData;
        const selectedPokemon = selectedPokemonTemplate.cloneNode(true);
        const images = selectedPokemon.querySelectorAll('img.poke-image');
        images.forEach(img => {
            img.setAttribute('src', img.src.replace('[id]', Number(pokemonData.id)));
            img.setAttribute('alt', img.alt.replace('{{name}}', pokemonData.name));
        });
        selectedPokemon.querySelector('span').textContent = pokemonData.name;
        const previewContainer = document.getElementById('poke-preview');
        previewContainer.innerHTML = '';
        previewContainer.appendChild(selectedPokemon);
        setAutoNextStep();
    }

    function handleShinyChange(event, element) {
        const isShiny = element.value === '1';
        const previewContainer = document.getElementById('poke-preview');
        if (!previewContainer.children.length) return;
        previewContainer.setAttribute('data-shiny', isShiny);
    }


    function searchPokemons(event, input) {
        event.preventDefault();
        const searchTerm = input.value.toLowerCase();
        if (!searchTerm || searchTerm.length < 3) return;
        const foundPokemons = pokemons.filter(pokemon => pokemon.name.toLowerCase().includes(searchTerm));
        if (!foundPokemons.length) return;

        pokemonListContainer.innerHTML = '';
        const fragment = document.createDocumentFragment();
        for (const foundPokemon of foundPokemons.slice(0, 50)) {
            const pokemonItem = pokeRadioItemTemplate.cloneNode(true);
            const tplInput = pokemonItem.querySelector('input');
            const tplImg = pokemonItem.querySelector('img');

            tplInput.setAttribute('value', foundPokemon.id);
            tplInput.setAttribute('required', true);
            tplInput.onchange = (event) => handlePokemonChange(event, tplInput);
            tplImg.setAttribute('src', foundPokemon.image);
            tplImg.setAttribute('alt', `${foundPokemon.name} icon`);
            pokemonItem.querySelector('span').textContent = foundPokemon.name;
            fragment.appendChild(pokemonItem);
        }
        pokemonListContainer.appendChild(fragment);
    }
</script>