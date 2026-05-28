# pokemon-hall-of-fame
Simple web app to register the different pokemon teams I completed the different games league with including names, levels and moves.

## DATOS
- Juegos:
    - id
    - nombre
    - version (api) // 'soulsilver'
    - version-group (api) // 'heartgold-soulsilver'
    - generacion (api)
    - imagen

- Ediciones (runs):
    - id
    - game_id (foreign key)
    - fecha estimada de completado
    - trainer_name
    - location

- Pokemon:
    - id
    - run_id (foreign key)
    - pokemon_id (api)
    - species (api)
    - level
    - nickname
    - gender
    - ability
    - item
    - types
    - genus
    - region
	- shiny (boolean)

- Moves:
    - pokemon_id (foreign key)
    - name (api)
    - type (api)