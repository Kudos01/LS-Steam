<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Interfaces;

use SallePW\SlimApp\Model\Money;

interface WalletRepository
{
    public function addAmount(int $user_id, float $amount): void;
    public function addDefaultAmount(int $user_id): void;
    public function getUserBalanace(int $user_id): float;
    public function removeAmount(int $user_id, float $amount): void;
}
