<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use DateTime;
use Exception;
use SallePW\SlimApp\Interfaces\CheapSharkRepository;
use SallePW\SlimApp\Interfaces\WalletRepository;
use Slim\Routing\RouteContext;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\UserRepository;
use SallePW\SlimApp\Utilities\SessionUtilities;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SallePW\SlimApp\Interfaces\UserGamesRepository;
use SallePW\SlimApp\Interfaces\WishlistRepository;
use SallePW\SlimApp\Model\UserGame;
use Slim\Flash\Messages;

final class StoreController
{
    public function __construct(
        private Twig $twig, 
        private CheapSharkRepository $cheapSharkRepository,
        private UserGamesRepository $userGamesRepository,
        private WalletRepository $walletRepository,
        private WishlistRepository $wishlistRepository,
        private Messages $flash
    )
    {
    }
    
    public function showStorePage(Request $request, Response $response): Response
    {
        $deals = json_decode($this->cheapSharkRepository->getListOfStoreGames());
        $errors = $this->flash->getMessages();
        
        //if we don't have any active sessions, redirect back to the login
        if(SessionUtilities::getSession() === -1){
            SessionUtilities::setNotLoggedInError();
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $response
                    ->withHeader('Location', $routeParser->urlFor("login"))
                    ->withStatus(302);
            exit;
        }
        //otherwise, show the store page
        else{
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            return $this->twig->render(
                $response,
                'store.twig',
                [
                    'errors' => $errors,
                    'deals' => $deals,
                ]
            );
        }
    }

    public function buyGame(Request $request, Response $response) : Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        
        $game_shark_id =(int) $request->getAttribute('gameId');
        $game_price = (double) $request->getParsedBody()['normalPrice'];
        $moneyError = '';

        if(SessionUtilities::getSession() != -1) {
            //Check if the user has enough money in the wallet
            //If yes, buy the game
            $user_wallet_amount = $this->walletRepository->getUserBalanace(SessionUtilities::getSession());
            $user_id = SessionUtilities::getSession();
            
            if($user_wallet_amount >= $game_price) {
                //Save gameId and userId in the user_games database
                $userGame = new UserGame(SessionUtilities::getSession(), $game_shark_id);
                $userGame->setUserId(SessionUtilities::getSession());
                $userGame->setGameSharkId($game_shark_id);
                $this->userGamesRepository->save($userGame);

                //Reduce wallet amount from user's account
                $this->walletRepository->removeAmount(SessionUtilities::getSession(), $game_price);
                $this->wishlistRepository->deleteGameFromWishlist($game_shark_id, $user_id);

                //Redirect to myGames page
                return $response
                    ->withHeader('Location', $routeParser->urlFor("my-games"))
                    ->withStatus(302);
                exit;
            }
        }

        $deals = json_decode($this->cheapSharkRepository->getListOfStoreGames());

        $this->flash->addMessage(
            'moneyError',
            'You don\'t have enough money to buy the game'
        );

        //Redirect to store page
        return $response
            ->withHeader('Location', $routeParser->urlFor("store"))
            ->withStatus(302);
        exit;
    }

    public function myGames(Request $request, Response $response) : Response
    {
        $myGamesIds = $this->userGamesRepository->getUserGamesIds(SessionUtilities::getSession());

        $myGames = $this->cheapSharkRepository->getGames($myGamesIds);

        return $this->twig->render(
            $response,
            'myGames.twig',
            [
                'myGames' => $myGames,   
            ]
        );
    }
}
