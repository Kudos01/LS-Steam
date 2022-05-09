<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Utilities;

use DateTime;
use Exception;
use Slim\Routing\RouteContext;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Interfaces\UserRepository;
use Slim\Views\Twig;

final class SessionUtilities {
    
    public static function setSession(int $user_id): void
    {
        $_SESSION['user_id'] = $user_id;
    }

    public static function setNotLoggedInError(): void
    {
        $_SESSION['not_logged_in'] = "Please Log in before accessing that page";
    }

    public static function setRequestError(): void
    {
        $_SESSION['friends'] = "You cannot do that!";
    }

    public static function getRequestError(): ?String
    {
        return $_SESSION['friends'] ?? null;
    }
    
    public static function unsetRequestError(): void
    {
        $_SESSION['friends'] = null;
    }

    public static function unsetNotLoggedInError(): void
    {
        $_SESSION['not_logged_in'] = null;
    }

    public static function getNotLoggedInError(): ?String
    {
        return $_SESSION['not_logged_in'] ?? null;
    }
     
    public static function unsetSession(): void
    {
        session_unset();
    }

    public static function getSession(): int
    {
        return $_SESSION['user_id'] ?? -1;
    }

    public static function setPicture(String $picture): void
    {
        $_SESSION['picture'] = $picture;
    }

    public static function getPicture(): ?String
    {
        return $_SESSION['picture'] ?? null;
    }

}
