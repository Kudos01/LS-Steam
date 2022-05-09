<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Model;

use DateTime;

final class UserGames
{
    private int $user_id;
    private $game_shark_ids = array();

    public function __construct(
        int $user_id
    ) {
        $this->user_id = $user_id;
    }

    public function addGame(string $game_shark_id) : void{
        $game_shark_ids[] = $game_shark_id;
    }

    public function getGames() : array {
        return $game_shark_ids;
    }
}
