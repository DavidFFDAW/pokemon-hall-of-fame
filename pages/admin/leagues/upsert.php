<?php
$id = $_GET['id'] ?? null;
$games = new Games();
$leaguesModel = new Leagues();
$gamesList = $games->get("ORDER BY generation ASC");
$regions = PokeApi::getApiData('/pokedex?limit=1000')['results'] ?? [];

$league = null;
if ($id) $league = $leaguesModel->find((int) $id);
$isUpdate = boolval($id) && $id !== 0 && ($league['id'] ?? null);

if (is_post_request()) {
    $post = new PostController();
    $post->upsertLeague();
    redirect('/admin/leagues');
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
        display: flex;
        padding: 0 0 50px 0;
        gap: 10px;
    }

    .upsert-datas-container img+div {
        flex: 1;
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .upsert-datas-container label.label {
        margin: 0;
    }

    #game_image_preview {
        max-width: 200px;
        max-height: 200px;
        aspect-ratio: 1 / 1;
        object-fit: contain;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

	.games-list {
		display: flex;
		max-width: 100%;
		overflow-x: auto;
		overflow-y: hidden;
		align-items: center;
		padding: 0 0 4px 0;
		gap: 10px;
	}
	.games-list .label {
		width: 180px;
		height: 100%;
	}
	.games-list .label .label-inner {
		display: flex;
		flex-direction: column;
		font-family: 'micrograma', sans-serif;
		text-transform: uppercase;
		font-size: 0.9em;
		gap: 6px;
	}
	.games-list .label .label-inner img {
		width: 100%;
		min-width: 100%;
		max-width: 100px;
		max-height: 100px;
		object-fit: cover;
		border: 1px solid #ccc;
		border-radius: 4px;
	}
	

</style>

<div class="box">
    <h2><?= $league ? 'Editar partida' : 'Agregar nueva partida'; ?></h2>

    <form action="" method="POST">
        <input type="hidden" name="id" value="<?= $league['id'] ?? ''; ?>" />
        <input type="hidden" name="action" value="<?= $isUpdate ? 'update' : 'create'; ?>" />

		<div class="games-list">
			<?php foreach ($gamesList as $game): ?>
				<label class="label radio-label">
					<input type="radio"
						name="game_id"
						value="<?= $game['id']; ?>" <?= (isset($league['game_id']) && $league['game_id'] === $game['id']) ? 'checked' : ''; ?>
						onchange="handleGameChange(event, this)"
						required
					/>
					<div class="label label-inner">
						<img src="<?= $game['image']; ?>" alt="<?= $game['name']; ?> image" />
						<?= $game['name']; ?>
					</div>
				</label>
			<?php endforeach; ?>
		</div>

        <div class="upsert-datas-container">
            <div class="">
                <label class="label">
                    <span class="label">Nombre de entrenador</span>
                    <input type="text" id="trainer_name" name="trainer_name" value="<?= $league['trainer_name'] ?? ''; ?>" required>
                </label>

                <label class="label">
                    <span class="label">Fecha</span>
                    <input type="date" name="date" value="<?= $league['date'] ?? ''; ?>" required>
                </label>
            </div>
        </div>

        <div class="game-version-container">
            <?php foreach ($regions as $region): ?>
                <label class="label radio-label">
                    <input type="radio"
                        name="region"
                        value="<?= $region['name']; ?>" <?= (isset($league['location']) && $league['location'] === $region['name']) ? 'checked' : ''; ?>
                        onchange="handleRegionChange(event, this)"
                        required
                    />
                    <span class="label"><?= $region['name']; ?></span>
                </label>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn"><?= $isUpdate ? 'Actualizar' : 'Crear'; ?></button>
    </form>
</div>

<script type="text/javascript">
    const $gameKeyInput = document.getElementById('game_key');
    const $gameImagePreview = document.getElementById('game_image_preview');

	function handleGameChange(event, element) {
		element.scrollIntoView({ behavior: 'smooth', inline: 'center' });
	}

    function handleErrorImage(event, element) {
        element.src = element.dataset.default;
    }
</script>
