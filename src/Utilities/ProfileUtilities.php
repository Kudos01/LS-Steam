<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Utilities;

use DateTime;
use Exception;
use Slim\Routing\RouteContext;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Interfaces\UserRepository;
use Slim\Views\Twig;

final class ProfileUtilities {

    public function __construct(private UserRepository $userRepository)
    {
    }
    
    public static function getUserImage(int $user_id): String
    {
        return $this->userRepository->getImageByUserId($user_id);
    }

}
