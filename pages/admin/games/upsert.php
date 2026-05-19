<?php
$id = $_GET['id'] ?? null;
$games = PokeApi::getGames();
$regions = PokeApi::getRegions();
$gamesModel = new Games(Database::getInstance());

$game = null;
if ($id) $game = $gamesModel->getGameById($id);

$isUpdate = boolval($id) && $id !== 0 && ($game['id'] ?? null);

try {
	if (is_post_request()) {
		$upserted = $gamesModel->upsert($_POST);
		if ($upserted) redirect('/admin/games');
	}
} catch (Exception $e) {
	Flash::add('Error al guardar el juego: ' . $e->getMessage(), 'error');
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
		gap: 10px;
		padding: 0 0 50px 0;
	}

	.upsert-datas-container label.label {
		margin: 0;
	}
</style>

<div class="box">
	<h2><?= $game ? 'Editar juego' : 'Agregar nuevo juego'; ?></h2>

	<form action="" method="POST">
		<input type="hidden" name="id" value="<?= $game['id'] ?? ''; ?>" />
		<input type="hidden" name="action" value="<?= $isUpdate ? 'update' : 'create'; ?>" />
		<input type="hidden" name="game_key" id="game_key" value="<?= $game['game_key'] ?? ''; ?>" />

		<div class="upsert-datas-container">
			<label class="label">
				<span class="label">Nombre de juego</span>
				<input type="text" id="game_name" name="game_name" value="<?= $game['game_name'] ?? ''; ?>" required>
			</label>

			<label class="label">
				<span class="label">Nombre del jugador</span>
				<input type="text" id="player_name" name="player_name" value="<?= $game['player_name'] ?? ''; ?>" required>
			</label>

			<label class="label">
				<span class="label">Region</span>
				<select id="region" name="region" required>
					<?php foreach ($regions as $regionOption): ?>
						<option value="<?= $regionOption; ?>" <?= (isset($game['region']) && $game['region'] === $regionOption) ? 'selected' : ''; ?>>
							<?= $regionOption; ?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>

			<!-- <label class="label">
                <span class="label">Fecha de inicio</span>
                <input type="date" id="start_date" name="start_date" value="<?= $game['start_date'] ?? ''; ?>">
            </label> -->

			<label class="label">
				<span class="label">Fecha de campeonato de liga</span>
				<input type="date" id="league_championship_date" name="league_championship_date" value="<?= $game['league_championship_date'] ?? ''; ?>">
			</label>
		</div>

		<div class="game-version-container">
			<?php foreach ($games as $gameOption): ?>
				<label class="label radio-label">
					<input type="radio"
						name="game_version"
						value="<?= $gameOption; ?>" <?= (isset($game['game_version']) && $game['game_version'] === $gameOption) ? 'checked' : ''; ?>
						onchange="handleGameVersionChange(event, this)">
					<span class="label"><?= $gameOption; ?></span>
				</label>
			<?php endforeach; ?>
		</div>

		<button type="submit" class="btn"><?= $isUpdate ? 'Actualizar' : 'Crear'; ?></button>
	</form>
</div>

<script type="text/javascript">
	const $gameKeyInput = document.getElementById('game_key');

	function handleGameVersionChange(event, element) {
		const selectedVersion = element.value;
		if (!selectedVersion) return;
		$gameKeyInput.value = selectedVersion.toLowerCase().replace(/\s+/g, '-');
	}
</script>