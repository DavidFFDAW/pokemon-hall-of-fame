<?php
$pokemon = 149;
$moves = PokeApi::getPokemonMoves($pokemon);
$pokemonName = array_values($moves)[0]['pokemon'] ?? 'Unknown';
// debug($moves);
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

<h1>Movimientos de <?= $pokemonName; ?></h1>

<div class="moves">
	<?php foreach ($moves as $move): ?>
		<label class="label radio-label">
			<input type="checkbox" name="moves[]" value="<?= $move['name'] ?? ''; ?>" onchange="handleChangeMove(event)" />
			
			<div class="move label">
				<img src="https://raw.githubusercontent.com/duiker101/pokemon-type-svg-icons/refs/heads/master/icons/<?= $move['type'] ?? 'normal' ?>.svg" alt="<?= $move['type'] ?? 'normal' ?>" class="move-type-icon type type-<?= $move['type'] ?? 'normal' ?>" />
				<span class="move-name"><?= $move['name'] ?? 'Unknown' ?></span>
			</div>
		</label>
	<?php endforeach; ?>
</div>

<script>
	function handleChangeMove(event) {
		const checks = document.querySelectorAll('input[name="moves[]"]:checked');
		if (checks.length > 4) {
			event.target.checked = false;
		}
	}
</script>