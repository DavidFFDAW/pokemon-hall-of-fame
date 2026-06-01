<?php

class PostController
{
    public function __construct() {}

    private function isset($data): bool
    {
        return isset($data) && !empty($data);
    }

    private function isUpdate(): bool
    {
        if (!isset($_POST['update_id'])) return false;
        return $this->isset($_POST['update_id']) && $_POST['action'] === 'update';
    }

    private function checkForFields(array $fields, array $datas = []): bool
    {
        $datas = $datas ?: $_POST;

        foreach ($fields as $field) {
            if (!$this->isset($datas[$field])) {
                throw new Exception("El campo '$field' es obligatorio.", 400);
            }
        }
        return true;
    }

    public function upsertGame()
    {
        $version = $_POST['version'] ?? '';
        $game_version = PokeApi::getApiData('version/' . $version);
        $group = $game_version['version_group']['name'] ?? '';
        $isUpdate = $this->isUpdate();

        $datas = array(
            'name' => $_POST['name'] ?? '',
            'version' => $version,
            'version_group' => $group,
            'generation' => $_POST['generation'] ?? 0,
            'image' => $_POST['image'] ?? '',
        );
        $games = new Games();
        $upserted = $isUpdate
            ? $games->update($datas, $_POST['update_id'])
            : $games->insert($datas);

        if (!$upserted) {
            Flash::add('Ocurrió un error al guardar el juego. Inténtalo de nuevo.', 'error');
        }
    }

    public function upsertLeague()
    {
        $league = new Leagues();
        $isUpdate = $this->isUpdate();

        $datas = array(
            'game_id' => $_POST['game_id'] ?? null,
            'trainer_name' => $_POST['trainer_name'] ?? '',
            'location' => $_POST['location'] ?? '',
            'date' => $_POST['date'] ?? null,
        );

        $this->checkForFields(['game_id', 'trainer_name', 'location', 'date'], $datas);
        $upserted = $isUpdate
            ? $league->update($datas, $_POST['update_id'])
            : $league->insert($datas);

        Flash::add(
            $upserted
                ? ($isUpdate ? 'Liga actualizada exitosamente.' : 'Liga creada exitosamente.')
                : 'Ocurrió un error al guardar la liga. Inténtalo de nuevo.',
            $upserted ? 'success' : 'error'
        );
    }

    public function handlePokemonUpsert()
    {
        $isUpdate = $this->isUpdate();
        $pokemon_id = $_POST['pokemon_id'] ?? null;
        $pokemon_data = PokeApi::getPokemonById($pokemon_id);
        $species_name = $pokemon_data['species']['name'] ?? '';
        $species_data = PokeApi::getApiData($pokemon_data['species']['url'] ?? '', true);
        $genus = PokeApi::getLanguageFromLanguagesArray($species_data['genera'] ?? [], 'es', 'genus');
        $poke_types = implode('-', array_map(function ($type) {
            return $type['type']['name'] ?? '';
        }, $pokemon_data['types'] ?? []));
        $is_legendary = $pokemon_data['is_legendary'] ?? false;

        $upsertDatas = array(
            'api_id' => $pokemon_id,
            'league_id' => $_POST['league_id'] ?? null,
            'species' => $species_name,
            'is_shiny' => $_POST['is_shiny'] ?? 0,
            'level' => $_POST['level'] ?? null,
            'nickname' => $_POST['nickname'] ?? '',
            'gender' => $_POST['gender'] ?? 'm',
            'ability' => $_POST['ability'] ?? '',
            'item' => $_POST['item'] ?? '',
            'types' => $poke_types,
            'genus' => $genus,
            'region' => $_POST['region'] ?? '',
            'nature' => $_POST['nature'] ?? '',
            'is_legendary' => $is_legendary ? 1 : 0,
        );

        $pokemons = new Pokemons();
        $this->checkForFields($pokemons->getRequiredFields(), $upsertDatas);

        $upserted = $isUpdate
            ? $pokemons->update($upsertDatas, $_POST['update_id'])
            : $pokemons->insert($upsertDatas);

        if (!$upserted) {
            Flash::add('Ocurrió un error al guardar el Pokémon. Inténtalo de nuevo.', 'error');
            return false;
        }

        Flash::add($isUpdate ? 'Pokémon actualizado exitosamente.' : 'Pokémon creado exitosamente.', 'success');
        return $upserted;
    }
}
