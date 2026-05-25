<?php

class Leagues extends Repository
{
	protected $table = 'leagues';

	public function getLeagues(): array
	{
		return $this->query('SELECT l.*, g.image, g.name FROM ' . $this->table . ' l JOIN games g ON l.game_id = g.id ORDER BY l.date ASC');
	}
}
