<!DOCTYPE html>
<html lang="<?= e($lang ?? 'es') ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($description ?? 'Poke Fame') ?>">
    <title><?= e($title ?? 'Poke Fame') ?></title>
    <link rel="stylesheet" href="<?= CSS_URI ?>/global.css">
</head>

<body>
    <main class="main-content">
		<div class="loading-container">
			<div class="loading-spinner"></div>
		</div>
		
        <?= $content ?>
    </main>

	<script type="text/javascript" src="<?= JS_URI ?>/global.js"></script>
</body>

</html>