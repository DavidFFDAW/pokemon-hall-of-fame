<?php

class PostController
{
    public function __construct()
    {
    }

    private function isset($data): bool {
        return isset($data) && !empty($data);
    }

    public function upsertGame()
    {
        $version = $_POST['version'] ?? '';
        $game_version = PokeApi::getApiData('version/' . $version);
        $group = $game_version['version_group']['name'] ?? '';
        $isUpdate = $this->isset($_POST['id']) && $_POST['action'] === 'update';

        $datas = array(
            'name' => $_POST['name'] ?? '',
            'version' => $version,
            'version_group' => $group,
            'generation' => $_POST['generation'] ?? 0,
            'image' => $_POST['image'] ?? '',
        );
        $games = new Games();
        $upserted = $isUpdate
            ? $games->update($datas, $_POST['id'])
            : $games->create($datas);

        if (!$upserted) {
            Flash::add('error', 'Ocurrió un error al guardar el juego. Inténtalo de nuevo.');
        }
        redirect('/admin/games');
    }
}