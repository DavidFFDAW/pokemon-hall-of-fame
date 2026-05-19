<?php
$db = new Pokemon(Database::getInstance());
$pokemons = $db->getPokemons();
?>

<div class="box">
	<h2>Pokémons registrados</h2>
    <?php if (!empty($pokemons)): ?>
		<div class="overflowable-table">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Mote</th>
                    <th>Juego</th>
                    <th>Nivel</th>
					<th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($pokemons as $pokemon): ?>
                    <tr>
                        <td><?= $pokemon['id'] ?></td>
                        <td><?= $pokemon['nickname'] ?></td>
                        <td><?= $pokemon['game_name'] ?></td>
                        <td><?= $pokemon['level'] ?></td>
						<td>
							<a href="<?= PATHNAME; ?>/upsert?id=<?= $pokemon['id'] ?>&game_id=<?= $pokemon['game_id'] ?>" class="btn" aria-label="Editar pokémon">Editar</a>
						</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
		</div>
    <?php else: ?>
        <p>No hay pokémons registrados.</p>
    <?php endif; ?>

    <a href="<?= PATHNAME; ?>/upsert" class="btn" aria-label="Agregar nuevo pokémon">Agregar nuevo pokémon</a>
</div>