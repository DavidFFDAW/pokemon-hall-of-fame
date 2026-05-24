<?php
$db = new Games();
$games = $db->getGames();
?>

<div class="box">
	<h2>Juegos registrados</h2>
    <?php if (!empty($games)): ?>
		<div class="overflowable-table">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Key</th>
                    <th>Juego</th>
                    <th>Jugador</th>
                    <th>Region</th>
					<th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($games as $game): ?>
                    <tr>
                        <td><?= $game['id'] ?></td>
                        <td><?= $game['game_key'] ?></td>
                        <td><?= $game['game_name'] ?></td>
                        <td><?= $game['player_name'] ?></td>
                        <td><?= $game['region'] ?></td>
                        <td><a href="/admin/pokemons/upsert?game_id=<?= $game['id'] ?>" class="btn" aria-label="Editar juego">Añadir pokemon</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
		</div>
    <?php else: ?>
        <p>No hay juegos registrados.</p>
    <?php endif; ?>

    <a href="<?= PATHNAME; ?>/upsert" class="btn" aria-label="Agregar nuevo juego">Agregar nuevo juego</a>
</div>