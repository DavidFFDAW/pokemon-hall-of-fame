<?php

$id = $_GET['id'] ?? null;
$leaguesModel = new Leagues();
if (!isset($id) || !$id || !is_numeric($id)) Flash::add('ID de liga no válido.', 'error');

$deleted = $leaguesModel->delete((int) $id);
if (!$deleted) Flash::add('Ocurrió un error al eliminar la liga. Inténtalo de nuevo.', 'error');

Flash::add('Liga eliminada exitosamente.', 'success');
redirect('/admin/leagues');
