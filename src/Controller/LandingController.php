<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use DateTime;
use Exception;
use Slim\Routing\RouteContext;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\UserRepository;
use SallePW\SlimApp\Utilities\SessionUtilities;
use SallePW\SlimApp\Utilities\LandingPageErrorUtilities;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class LandingController
{
    public function __construct(private Twig $twig)
    {
    }
    
    public function showLandingPage(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $this->twig->render(
            $response,
            'landing.twig'
        );
    }
}
