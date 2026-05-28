<?php
$dir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
include $dir . 'functions.php';
include $dir . 'classes/PokeApi.php';

function get_item(string $url, string $category): ?array
{
	$itemData = PokeApi::fetch($url, true);
	if (!$itemData) return null;

	$names = array_filter($itemData['names'] ?? [], function ($name) {
		return isset($name['language']['name']) && $name['language']['name'] === 'es';
	});
	$esEffects = array_filter($itemData['effect_entries'] ?? [], function ($entry) {
		return isset($entry['language']['name']) && $entry['language']['name'] === 'es';
	});

	return [
		'name' => $itemData['name'] ?? 'Unknown',
		'nombre' => array_values($names)[0]['name'] ?? 'Unknown',
		'effect' => array_values($esEffects)[0]['effect'] ?? 'No description available.',
		'image' => $itemData['sprites']['default'] ?? '',
		'category' => $category,
	];
}

$categories = array(
	'held-items',
	'choice',
	'effort-training',
	'species-specific',
	'type-enhancement',
	'jewels'
);

$items = [];
foreach ($categories as $category) {
	$url = "https://pokeapi.co/api/v2/item-category/{$category}/?limit=10000";
	$categoryData = PokeApi::fetch($url, true);
	if (!$categoryData || !isset($categoryData['items'])) continue;

	$_items = $categoryData['items'];
	foreach ($_items as $item) {
		$itemDetails = get_item($item['url'] ?? '', $category);
		if ($itemDetails) $items[] = $itemDetails;
	}
}

// debug($items);
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Items</title>
	<style>
		*,
		*::after,
		*::before {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		html,
		body {
			min-height: 100dvh;
			padding: 20px 15px;
			font-family: Arial, sans-serif;
		}

		body {
			max-width: 800px;
			margin: 0 auto;
		}

		.items-container {
			display: grid;
			grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
			gap: 20px;
		}

		.item {
			padding: 15px 20px;
			border: 1px solid #ccc;
			background-color: #eee;
			border-radius: 8px;
			overflow: hidden;
			box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
			display: flex;
			flex-direction: column;
		}

		.item .item-header {
			display: flex;
			align-items: center;
			margin-bottom: 10px;
			gap: 15px;
		}

		.item .item-header img {
			width: 50px;
			height: 50px;
			object-fit: contain;
		}

		.item .item-header p {
			font-size: 1.2em;
			font-weight: bold;
		}

		.item .item-content {
			flex-grow: 1;
			background-color: rgba(0, 0, 0, 0.3);
			padding: 10px 15px;
			border-radius: 5px;
			color: #fff;
			font-size: 0.9em;
		}
	</style>
</head>

<body>
	<div class="items-container">
		<?php foreach ($items as $item): ?>
			<div class="item">
				<div class="item-header">
					<img src="<?= e($item['image'] ?? '') ?>" alt="<?= e($item['nombre'] ?? $item['name'] ?? 'Unknown') ?>" />
					<p><?= e($item['nombre'] ?? $item['name'] ?? 'Unknown') ?></p>
				</div>
				<div class="item-content">
					<span class="item-effect">
						<?= e($item['effect'] ?? 'No description available.') ?>
					</span>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<script>
		const items = document.querySelectorAll('.items-container .item');
		const parsed = Array.from(items).map(item => {
			const name = item.querySelector('.item-header p');
			const image = item.querySelector('.item-header img');
			const effect = item.querySelector('.item-content .item-effect');

			return {
				name: name ? name.textContent.trim() : 'Unknown',
				image: image ? image.src : '',
				effect: effect ? effect.textContent.trim() : 'No description available.'
			};
		});
		console.log(parsed);
	</script>
</body>

</html>