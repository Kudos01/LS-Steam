<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Model;

use DateTime;

final class FriendRequest
{
    private int $request_id;
    private int $user_1_id;
    private int $user_2_id;
    private String $accept_date = "\0";
    private String $requester_username;
    private int $accepted;

    public function __construct(
        int $request_id,
        int $user_1_id,
        int $user_2_id,
        int $accepted

    ) {
        $this->request_id = $request_id;
        $this->user_1_id = $user_1_id;
        $this->user_2_id = $user_2_id;
        $this->accepted = $accepted;
    }

    public function request_id(): int
    {
        return $this->request_id;
    }

    public function set_request_id(int $request_id): void
    {
        $this->request_id = $request_id;
    }

    public function user_1_id(): string
    {
        return $this->user_1_id;
    }

    public function set_user_1_id(int $user_1_id): void
    {
        $this->user_1_id = $user_1_id;
    }

    public function user_2_id(): int
    {
        return $this->user_2_id;
    }

    public function set_user_2_id(int $user_2_id): void
    {
        $this->user_2_id = $user_2_id;
    }

    public function accept_date(): String
    {
        return $this->accept_date;
    }

    public function set_accept_date(String $accept_date): void
    {
        $this->accept_date = $accept_date;
    }
    public function set_accepted(): void
    {
        $this->accepted = 1;
    }
    public function accepted(): int
    {
        return $this->accepted;
    }
     public function requester_username(): String
    {
        return $this->requester_username;
    }

    public function set_requester_username(String $requester_username): void
    {
        $this->requester_username = $requester_username;
    }


}
