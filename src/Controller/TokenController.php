<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use SallePW\SlimApp\Interfaces\TokenRepository;
use SallePW\SlimApp\Interfaces\UserRepository;
use SallePW\SlimApp\Interfaces\WalletRepository;

use SallePW\SlimApp\Utilities\EmailHandler;
use SallePW\SlimApp\Utilities\SessionUtilities;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class TokenController
{
    public function __construct(
        private Twig $twig, 
        private TokenRepository $tokenRepository, 
        private EmailHandler $emailHandler, 
        private UserRepository $userRepository,
        private WalletRepository $walletRepository)
    {}

    public function redeemToken(Request $request, Response $response, $args): Response {
        $token_value =  $request->getQueryParams()['token'];
        $token = $this->tokenRepository->getToken($token_value);

        //Check if the token is valid
        if($token != null && !$token->isIsRedeemed()) {
            $this->tokenRepository->redeemToken($token);
            //Sending the registration confirmation, getting the user email based on the token id
            $this->emailHandler->sendRegistrationConfirmation($this->userRepository->getUserEmailByToken($token->getTokenValue()));

            //Add 50$ by default
            $user_id = $this->tokenRepository->getUserIdByToken($token_value);
            $this->walletRepository->addDefaultAmount($user_id);

            return $this->twig->render(
                $response,
                'redeemToken.twig',
                [
                    'tokenIsSuccessfullyRedeemed' => true,
                    'tokenAlreadyUsed' => false,
                ]
            );
        }

        return $this->twig->render(
            $response,
            'redeemToken.twig',
            [
                'tokenIsSuccessfullyRedeemed' => false,
                'tokenAlreadyUsed' => true,
            ]
        );
    }
}