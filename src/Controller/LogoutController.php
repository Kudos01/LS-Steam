<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use DateTime;
use Exception;
use Slim\Routing\RouteContext;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\UserRepository;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SallePW\SlimApp\Utilities\SessionUtilities;

final class LogoutController{

    public function __construct(private Twig $twig)
    {
    }
    
    public function logoutUser(Request $request, Response $response): Response
    {
        SessionUtilities::unsetSession();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        return $response
                ->withHeader('Location', $routeParser->urlFor("landingPage"))
                ->withStatus(302);
        exit;
    }
}






