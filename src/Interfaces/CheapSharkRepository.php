<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Interfaces;

use SallePW\SlimApp\Model;

interface CheapSharkRepository
{
    public function getListOfStoreGames(): string;
    public function getGames(array $games_ids): array;
}
