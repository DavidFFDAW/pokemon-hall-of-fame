<?php 
if (is_post_request()) {
	response('error acaas', 200);
}
?>
<div class="pokemons">
	<form action="" method="post" data-async="true">
		<input type="text" name="name" placeholder="Nombre del Pokémon">
		<button type="submit">Buscar</button>
	</form>
</div>