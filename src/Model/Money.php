<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Model;

final class Money
{
    private int $money_id;
    private float $amount;
    private int $user_id;

    public function __construct(
        int $money_id,
        float $amount,
        int $user_id,
    ) {
        $this->money_id = $money_id;
        $this->amount = $amount;
        $this->user_id = $user_id;
    }
}
