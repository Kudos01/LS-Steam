<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use SallePW\SlimApp\Interfaces\WishlistRepository;
use SallePW\SlimApp\Interfaces\CheapSharkRepository;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SallePW\SlimApp\Repository\CheapSharkEndpointDecorator;
use SallePW\SlimApp\Utilities\CacheUtilities;
use SallePW\SlimApp\Utilities\SessionUtilities;
use Slim\Routing\RouteContext;

final class WishlistController{

    public function __construct(
        private Twig $twig,
        private WishlistRepository $wishlistRepository,
        private CheapSharkEndpointDecorator $cheapSharkEndpointDecorator
    ){}

    public function showWishlist (Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $user_id = SessionUtilities::getSession();

        $games_ids = $this->wishlistRepository->getWishlistGamesIds($user_id);

        $wishlist = $this->getWishlistGames($games_ids);
    
        return $this->twig->render(
            $response,
            'wishlist.twig',
            [
                'wishlist'=>$wishlist
            ]
        );
    }

    private function getWishlistGames(array $games_ids) : array {
        $games = json_decode($this->cheapSharkEndpointDecorator->getListOfStoreGames(), true);
        $wishlist = array();
        $ids = array();

        //Convert $games_ids into one array
        foreach($games_ids as $id){
            array_push($ids, $id[0]);
        }

        //Compare gameID from the $games array with the ids of the users' wishlist
        //Filter the results based on the $ids array

        $wishlist = array_filter($games, function($v) use ($ids) {
            return in_array($v['gameID'], $ids);
        });

        return $wishlist;
    }

    public function addGameToWishlist (Request $request, Response $response): Response 
    {
        $game_shark_id = (int) $request->getAttribute('gameId');
        $user_id = SessionUtilities::getSession();

        //Check if the game is already added to the wishlist of the user
        $isGameAlreadyAdded = $this->wishlistRepository->checkIfTheGameIsOnTheList($game_shark_id, $user_id);

        if(!$isGameAlreadyAdded){
            $this->wishlistRepository->addGameToWishlist($game_shark_id, $user_id);
        }

        //Once the game was added, stay on the store page
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        return $response
                    ->withHeader('Location', $routeParser->urlFor("store"))
                    ->withStatus(302);
    }

    public function deleteGameFromWishlist(Request $request, Response $response) : Response 
    {
        $game_shark_id = (int) $request->getAttribute('gameId');
        $user_id = SessionUtilities::getSession();

        $this->wishlistRepository->deleteGameFromWishlist($game_shark_id, $user_id);

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        return $response
                    ->withHeader('Location', $routeParser->urlFor("show-wishlist"))
                    ->withStatus(303);
    }

    public function displayGameDetails(Request $request, Response $response) : Response 
    {
        $game_shark_id = (int) $request->getAttribute('gameId');

        $games = json_decode($this->cheapSharkEndpointDecorator->getListOfStoreGames(), true);

        $game = array_filter($games, function($item) use ($game_shark_id){
            return $item['gameID'] == $game_shark_id;
        });

        return $this->twig->render(
            $response,
            'gameDetails.twig',
            [
                'deal'=>array_values($game)[0] //returning the first element in the $game array
            ]
        );
    }
}