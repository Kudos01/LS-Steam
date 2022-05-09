<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use DateTime;
use Exception;
use Slim\Routing\RouteContext;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Interfaces\UserRepository;
use SallePW\SlimApp\Interfaces\WalletRepository;
use SallePW\SlimApp\Utilities\SessionUtilities;
use SallePW\SlimApp\Utilities\LandingPageErrorUtilities;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class WalletController
{
    public function __construct(
        private Twig $twig, 
        private UserRepository $userRepository, 
        private WalletRepository $walletRepository
        )
    {
    }
    
    public function showWalletForm(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if(SessionUtilities::getSession() === -1){
            SessionUtilities::setNotLoggedInError();
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $response
                    ->withHeader('Location', $routeParser->urlFor("login"))
                    ->withStatus(302);
            exit;
        }else{

            return $this->twig->render(
                $response,
                'wallet.twig',
                [
                    'formAction' => $routeParser->urlFor("handle-wallet"),
                    'formMethod' => "POST",
                    
                    'money_amount' => $this->walletRepository->getUserBalanace(SessionUtilities::getSession())
                ]
            );
        }
    }

    public function handleWalletForm(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $errors = [];

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if($data['money'] <= 0 || !is_numeric($data['money'])){
            $errors['money'] = 'Please input a valid amount';
        }

        if (empty($errors)) {
            $this->walletRepository->addAmount(SessionUtilities::getSession(), (float) $data['money']);
        }

        return $this->twig->render(
            $response,
            'wallet.twig',
            [
                'formErrors' => $errors,
                'formAction' => $routeParser->urlFor("handle-wallet"),
                'formMethod' => "POST",
                'money_amount' => $this->walletRepository->getUserBalanace(SessionUtilities::getSession())
            ]
        );
    }

}
