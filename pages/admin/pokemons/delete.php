<?php
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id) || $id <= 0 || empty($id)) {
	Flash::add('ID de pokémon no proporcionado o inválido para eliminar.', 'error');
	redirect('/admin/pokemons');
	exit;
}

$conn = Database::getInstance()->getConnection();
$stmt = $conn->prepare('DELETE FROM pokemons WHERE id = :id');
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

Flash::add('Pokémon eliminado correctamente.', 'success');
redirect('/admin/pokemons');
exit;