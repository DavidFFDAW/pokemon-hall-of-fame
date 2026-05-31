CREATE TABLE IF NOT EXISTS `games` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `version` VARCHAR(50) NOT NULL,
    `version_group` VARCHAR(50) NOT NULL,
    `generation` INT(3) NOT NULL,
    `image` VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS `leagues` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `game_id` INT NOT NULL,
    `trainer_name` VARCHAR(50) NOT NULL,
    `location` VARCHAR(20) NULL,
    `date` DATE NOT NULL,
    FOREIGN KEY (game_id) REFERENCES games(id)
);

CREATE TABLE IF NOT EXISTS `pokemon` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `api_id` INT NOT NULL,
    `league_id` INT NOT NULL,
    `nickname` VARCHAR(50) NOT NULL,
    `species` VARCHAR(50) NOT NULL,
    `level` INT(3) NOT NULL,
    `gender` VARCHAR(1) NOT NULL,
    `ability` VARCHAR(25) NULL DEFAULT NULL,
    `item` VARCHAR(25) NULL DEFAULT NULL,
    `types` VARCHAR(20) NULL DEFAULT NULL,
    `genus` VARCHAR(50) NULL DEFAULT NULL,
    `region` VARCHAR(20) NULL DEFAULT NULL,
    `is_shiny` BOOLEAN NULL DEFAULT NULL,
    `nature` VARCHAR(20) NULL DEFAULT NULL,
    `is_legendary` BOOLEAN NULL DEFAULT NULL,
    FOREIGN KEY (league_id) REFERENCES leagues(id)
);

CREATE TABLE IF NOT EXISTS `moves` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `api_id` INT NOT NULL,
    `type` VARCHAR(20) NOT NULL,
    `en_name` VARCHAR(20) NOT NULL,
    `name` VARCHAR(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS `pokemon_moves` (
    `pokemon_id` INT NOT NULL,
    `move_id` INT NOT NULL,
    PRIMARY KEY (pokemon_id, move_id),
    FOREIGN KEY (pokemon_id) REFERENCES pokemon(id),
    FOREIGN KEY (move_id) REFERENCES moves(id)
);