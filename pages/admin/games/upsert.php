<?php
$id = $_GET['id'] ?? null;
$gamesModel = new Games();
$games = PokeApi::getGames();

$game = null;
if ($id) $game = $gamesModel->find((int) $id);
$isUpdate = boolval($id) && $id !== 0 && ($game['id'] ?? null);

if (is_post_request()) {
    $post = new PostController();
    $post->upsertGame();
    redirect('/admin/games');
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
</style>

<div class="box">
    <h1 class="panel-title"><?= $game ? 'Editar juego' : 'Agregar nuevo juego'; ?></h1>

    <form action="" method="POST">
        <input type="hidden" name="update_id" value="<?= $game['id'] ?? ''; ?>" />
        <input type="hidden" name="action" value="<?= $isUpdate ? 'update' : 'create'; ?>" />

        <div class="upsert-datas-container">
            <img id="game_image_preview" src="<?= $game['image'] ?? DEFAULT_IMAGE; ?>" data-default="<?= DEFAULT_IMAGE; ?>" alt="Game cover" draggable="false" onerror="handleErrorImage(event, this)" />

            <div class="">
                <label class="label">
                    <span class="label">Nombre de juego</span>
                    <input type="text" id="game_name" name="name" value="<?= $game['name'] ?? ''; ?>" required>
                </label>

                <label class="label">
                    <span class="label">Image</span>
                    <input type="text" name="image" value="<?= $game['image'] ?? ''; ?>" required onchange="handleImageChange(event, this)">
                </label>

                <label class="label">
                    <span class="label">Generación</span>
                    <input type="number" name="generation" value="<?= $game['generation'] ?? ''; ?>" inputmode="numeric" required>
                </label>
            </div>
        </div>

        <div class="game-version-container">
            <?php foreach ($games as $gameOption): ?>
                <label class="label radio-label">
                    <input type="radio"
                        name="version"
                        value="<?= $gameOption; ?>" <?= (isset($game['version']) && $game['version'] === $gameOption) ? 'checked' : ''; ?>
                        onchange="handleGameVersionChange(event, this)"
                        required
                    />
                    <span class="label"><?= $gameOption; ?></span>
                </label>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn"><?= $isUpdate ? 'Actualizar' : 'Crear'; ?></button>
    </form>
</div>

<script type="text/javascript">
    const $gameKeyInput = document.getElementById('game_key');
    const $gameImagePreview = document.getElementById('game_image_preview');

    function handleGameVersionChange(event, element) {
        const selectedVersion = element.value;
        if (!selectedVersion) return;
        $gameKeyInput.value = selectedVersion.toLowerCase().replace(/\s+/g, '-');
    }

    function handleImageChange(event, element) {
        const imageUrl = element.value;
        if (!imageUrl) return;

        $gameImagePreview.src = imageUrl;
    }

    function handleErrorImage(event, element) {
        element.src = element.dataset.default;
    }
</script>
