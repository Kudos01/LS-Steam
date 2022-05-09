<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Interfaces;

interface WishlistRepository
{
    public function addGameToWishlist($game_shark_id, $user_id): void;
    public function deleteGameFromWishlist($game_shark_id, $user_id): void;
    public function getWishlistGamesIds($user_id): array;
    public function checkIfTheGameIsOnTheList($game_shark_id, $user_id): bool;
}
