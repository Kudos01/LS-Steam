<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Model;

use DateTime;

final class User
{
    private int $id;
    private string $login;
    private string $password;
    private DateTime $createdAt;

    public function __construct(
        string $login,
        string $password,
        DateTime $createdAt,
    ) {
        $this->login = $login;
        $this->password = $password;
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

    public function login(): string
    {
        return $this->login;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }
}
