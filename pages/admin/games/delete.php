<?php

$id = $_GET['id'] ?? null;
$gamesModel = new Games();
if (!isset($id) || !$id || !is_numeric($id)) Flash::add('ID de juego no válido.', 'error');

$deleted = $gamesModel->deleteById((int) $id);
if (!$deleted) Flash::add('Ocurrió un error al eliminar el juego. Inténtalo de nuevo.', 'error');

Flash::add('Juego eliminado exitosamente.', 'success');
redirect('/admin/games');
