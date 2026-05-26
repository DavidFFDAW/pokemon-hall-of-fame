<?php

class Leagues extends Repository
{
	protected $table = 'leagues';

	public function getLeagues(): array
	{
		return $this->query('SELECT l.*, g.image AS game_image, g.name AS game_name, g.generation AS game_gen FROM ' . $this->table . ' l JOIN games g ON l.game_id = g.id ORDER BY l.date ASC');
	}
}
