<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

final class StartSessionMiddleware
{
    public function __invoke(Request $request, RequestHandler $next): Response
    {
        session_start();
        
        //Setting global activation token url - used to send emails to the user
        $_SERVER['ACTIVATE_TOKEN_URL'] = '/activate';

        return $next->handle($request);
    }
}