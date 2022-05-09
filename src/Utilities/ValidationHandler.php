<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Utilities;

final class ValidationHandler{
    public function __construct(){

    }

    public function validateEmail(string $email) :bool 
    {
        $find1 = strpos($email, '@');
        $find2 = strpos($email, '.');

        return $find1 && $find2 
                //&& preg_match('~[0-9]~', $email) === 0 
                && !empty($email) 
                && filter_var($email, FILTER_VALIDATE_EMAIL)
                && strpos($email, "@salle.url.edu");
    }

    public function isPasswordValid(string $password) :bool
    {
        return strlen($password) > 6 
            && preg_match('~[0-9]~', $password) != 0 
            && preg_match('~[a-zA-Z]~', $password) != 0;
    }

}