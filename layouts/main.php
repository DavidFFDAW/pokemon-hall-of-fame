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
        <?= $content ?>
    </main>
</body>

</html>