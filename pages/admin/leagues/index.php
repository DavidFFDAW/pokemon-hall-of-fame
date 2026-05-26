<?php
$leagues = new Leagues();
$list = $leagues->getLeagues();
?>

<style>
    .games-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
    }
    .games-container .game-card {
        display: flex;
        position: relative;
        flex-direction: column;
        gap: 6px;
        
        border: 1px solid #ccc;
        background-color: #fff;
        text-align: center;
        border-radius: 8px;
        padding: 6px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .games-container .game-card .badge.badge-generation {
        top: 10px;
        left: 10px;
    }
    .games-container .game-card img {
        border-radius: 4px 4px 0 0;
        aspect-ratio: 1 / 1;
        object-fit: cover;
        max-width: 100%;
        height: 240px;
    }
    .games-container .game-card .game-card-text-content h3 {
        margin: 0;
		position: relative;
        font-size: 1.1em;
		font-weight: 400;
		text-transform: uppercase;
		margin-bottom: 6px;
    }
    .games-container .game-card .game-card-text-content h3::after {
		content: '';
		position: absolute;
		left: 50%;
		bottom: -2px;
		transform: translateX(-50%);
		width: 25%;
		height: 3px;
		border-radius: 50px;
		background-color: #b31d1d;
	}

    .games-container .game-card .game-card-text-content {
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .games-container .game-card a.btn.btn-delete {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: transparent;
        border: none;
        color: #fff;
        background-color: rgba(255, 0, 0, 0.8);
        padding: 4px 8px;
        font-weight: bold;
        font-size: .8em;
        cursor: pointer;
    }
    .games-container .game-card .game-card-text-content a.btn-update {
        align-self: center;
        background-color: #f9f2de;
        border: 1px solid #f0c36d;
        color: #e0a800;
        border-radius: 50px;
        padding: 6px 16px;
        text-decoration: none;
        font-weight: 400;
        font-size: 0.85rem;
    }
    .games-container .game-card .game-card-text-content a.btn-update:hover {
        background-color: #e0a800;
        border-color: #c69500;
        color: #fff;
    }
</style>

<div class="admin-panel-games-page">
	<h1 class="primetime uppercase tcenter">Partidas registradas</h1>

    <div class="games-container"> 
        <?php foreach ($list as $game): ?>
            <div class="game-card">
                <!-- <span class="badge badge-generation">G<?= $game['generation']; ?></span> -->
                <img src="<?= $game['game_image']; ?>" alt="<?= $game['game_name']; ?> image" class="game-image">
                <div class="game-card-text-content">
                    <h3 class="micrograma underline"><?= $game['trainer_name']; ?></h3>
                    <small><?= $game['game_name']; ?></small>
                    <a href="<?= PATHNAME; ?>/upsert?id=<?= $game['id']; ?>" class="btn btn-update" aria-label="Editar juego <?= $game['game_name']; ?>">Editar</a>
                </div>

                <a href="<?= PATHNAME; ?>/delete?id=<?= $game['id']; ?>" 
                    class="btn btn-delete" 
                    aria-label="Eliminar juego <?= $game['name']; ?>"
                    onclick="handleConfirm(event, '¿Estás seguro de que deseas eliminar esta partida? Esta acción no se puede deshacer.')"
                >x</a>
            </div>
        <?php endforeach; ?>
    </div>

    <a href="<?= PATHNAME; ?>/upsert" class="btn btn-fixed-create" aria-label="Agregar nueva partida">Agregar nueva partida</a>
</div>

<script>
function handleConfirm(event, message) {
	if (!confirm(message)) event.preventDefault();
}
</script>