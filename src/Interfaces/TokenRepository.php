<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Interfaces;

use SallePW\SlimApp\Model\Token;

interface TokenRepository
{
    public function saveToken(Token $token): void;
    public function redeemToken(Token $token): void;
    public function getToken(string $token_value): ? Token;
    public function isTokenRedeemed(int $user_id): ? bool;
    public function getUserIdByToken(string $token_value) : int;
}
