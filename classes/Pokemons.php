<?php

class Pokemons extends Repository
{
    protected $table = 'pokemon';
    protected $requiredFields = ['api_id', 'league_id', 'species', 'level', 'nickname', 'types'];
}
