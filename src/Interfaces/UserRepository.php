<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Interfaces;

use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\RegisteredUser;

interface UserRepository
{
    public function save(RegisteredUser $user): void;
    public function check(User $user): int;
    public function getUserEmailByToken(String $token) : ?string;
    public function checkUniqueUsername(String $username): int;
    public function getUserByUsername(String $username): int;
    public function checkUniqueEmail(String $email): int;
    public function getUserByID(int $user_id): RegisteredUser;
    public function updateProfilePictureByID(int $user_id, String $profile_uuid): void;
    public function updateTelephoneByID(int $user_id, String $phone): void;
    public function getImageByUserId(int $user_id): String;
}
