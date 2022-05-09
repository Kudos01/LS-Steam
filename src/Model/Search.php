<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Model;

use DateTime;

final class Search
{
    private int $id;
    private int $user_id;
    private string $search;
    private DateTime $createdAt;

    public function __construct(
        int $user_id,
        string $search,
        DateTime $createdAt
    ) {
        $this->search = $search;
        $this->user_id = $user_id;
        $this->createdAt = $createdAt;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function search(): string
    {
        return $this->search;
    }

    public function user_id(): int
    {
        return $this->user_id;
    }

    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }
}
