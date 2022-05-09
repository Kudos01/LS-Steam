<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Model;

use DateTime;

final class UserGame
{
    private int $game_id;
    private int $user_id;
    private int $game_shark_id;

    public function __construct(
        int $user_id,
        int $game_shark_id
    ) {
        $this->user_id = $user_id;
        $this->game_shark_id = $game_shark_id;
    }

    public function getUserId(): int {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self {
        $this->user_id = $user_id;
        return $this;
    }

    public function getGameSharkId(): int {
        return $this->game_shark_id;
    }

    public function setGameSharkId(int $game_shark_id): self {
        $this->game_shark_id = $game_shark_id;
        return $this;
    }
}
