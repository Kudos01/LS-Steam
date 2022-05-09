<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Utilities;

use DateTime;
use Exception;
use Slim\Routing\RouteContext;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Interfaces\UserRepository;
use Slim\Views\Twig;

define("CACHE_FILE_DIR",     "../public/cache/");
define("CACHE_FILE",          CACHE_FILE_DIR . CACHE_FILE_NAME);

final class CacheUtilities {
    
    public function isEmpty(): bool {
        return !file_exists(CACHE_FILE);
    }

    public function fillCache(String $games): void {
        // write to file
        file_put_contents(CACHE_FILE, $games);
    }

    public function getCache(): String {
        // read from file
        return file_get_contents(CACHE_FILE);
    }

}
