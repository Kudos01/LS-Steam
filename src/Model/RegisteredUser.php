<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Model;

use DateTime;

//define("DEFAULT_PICTURE",     "default_picture.png");

final class RegisteredUser
{
    private int $id;
    private string $username;
    private string $email;
    private string $password;
    private DateTime $birthdate;
    private string $phone_number;
    private DateTime $createdAt;
    private string $profile_uuid;

    public function __construct(
        string $username,
        string $email,
        string $password,
        DateTime $birthdate,
        string $phone_number,
        DateTime $createdAt,

    ) {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->birthdate = $birthdate;
        $this->phone_number = $phone_number;
        $this->createdAt = $createdAt;
        $this->profile_uuid = DEFAULT_PICTURE;
    }

    public function id(): int
    {
        return $this->id;
    }
    // TODO We dont really need this do we? 
    // No, there's no reason for any of the functions here to have id i think
    /*
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
    */

    public function username(): string
    {
        return $this->username;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function birthdate(): DateTime
    {
        return $this->birthdate;
    }

    public function phone_number(): string
    {
        return $this->phone_number;
    }

    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }
    public function profile_uuid(): string
    {
        return $this->profile_uuid;
    }
    public function setUsername(String $username): void{
        $this->username = $username;
    }
    public function setEmail(String $email): void{
        $this->email = $email;
    }
    public function setPhone(String $phone): void{
        $this->phone_number = $phone;
    }
    public function setBirthdate(DateTime $birthdate): void{
        $this->birthdate = $birthdate;
    }
    public function setProfileUUID(string $profile_uuid): void{
        $this->profile_uuid = $profile_uuid;
    }
    public function setDefaultProfileUUID(): void{
        $this->profile_uuid = "b9406c81-4727-4a5a-8a1f-5d141305db3c";
    }

}
