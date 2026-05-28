<!DOCTYPE html>
<html lang="<?= e($lang ?? 'es') ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="<?= e($robots ?? 'noindex,nofollow') ?>">
    <meta name="description" content="<?= e($description ?? 'Administracion de Poke Fame') ?>">
    <title><?= e($title ?? 'Admin | Poke Fame') ?></title>
    <link rel="stylesheet" href="<?= CSS_URI ?>/global.css" />
    <link rel="stylesheet" href="<?= CSS_URI ?>/admin.css" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
</head>

<body>
    <?php $flashMessages = Flash::get(); ?>
    <header class="admin-panel-header admin-header">
        <nav>
            <ul class="admin-nav-list">
                <li><a href="<?= BASE_URI ?>">Home</a></li>
                <li><a href="<?= BASE_URI ?>admin/dashboard">Admin</a></li>
                <li><a href="<?= BASE_URI ?>admin/users/list">Usuarios</a></li>
                <li><a href="<?= BASE_URI ?>admin/games">Juegos</a></li>
                <li><a href="<?= BASE_URI ?>admin/pokemons">Pokémons</a></li>
                <!-- Add more admin links as needed -->
            </ul>
        </nav>
    </header>

    <main class="admin-panel-content">
        <ul class="flash-messages">
            <?php foreach ($flashMessages as $flash): ?>
                <li class="flash-message flash-<?= e($flash['type']) ?>">
                    <?= e($flash['message']) ?>
                </li>
            <?php endforeach; ?>
        </ul>

		<div class="loading-container">
			<div class="loading-spinner"></div>
		</div>

        <?= $content ?>
    </main>

	<script type="text/javascript" src="<?= JS_URI ?>/global.js"></script>
</body>

</html>