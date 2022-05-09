<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Interfaces;

use SallePW\SlimApp\Model\UserGame;

interface UserGamesRepository
{
    public function save(UserGame $user_game): void;
    public function getUserGamesIds(int $user_id): array;
}
