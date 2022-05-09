<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Interfaces;

use SallePW\SlimApp\Model\FriendRequest;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\RegisteredUser;


interface FriendRepository
{
    public function getFriendsForUser(int $user_id_1): array;
    public function createNewFriendRequest(int $user_id_1, int $user_id_2): void;
    public function checkIfFriendAlreadyRequested(int $user_id_1, int $user_id_2): bool;
    public function getFriendRequestForUser(int $friend_request_to_id): array;
    public function setDateInDB(int $request_id): void;
    public function acceptFriendRequest(int $request_id): void;
    public function getToUserIdFromFriendRequest(int $request_id): int;
    
}
