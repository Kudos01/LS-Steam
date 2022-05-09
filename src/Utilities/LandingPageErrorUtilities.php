<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Utilities;

use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class LandingPageErrorUtilities {
    
    public static int $error_flag =0;

    public static function getError(): String
    {
        return "Please Log in before accessing that page!";
    }

}
