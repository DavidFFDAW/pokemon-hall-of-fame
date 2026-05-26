<?php

class PostController
{
    public function __construct()
    {
    }

    private function isset($data): bool {
        return isset($data) && !empty($data);
    }

	private function isUpdate(): bool {
		if (!isset($_POST['update_id'])) return false;
		return $this->isset($_POST['update_id']) && $_POST['action'] === 'update';
	}

	private function checkForFields(array $fields, array $datas = []): bool {
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

	public function upsertLeague() {
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
}