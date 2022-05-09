<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use DateTime;
use Exception;
use Ramsey\Uuid\Uuid;
use Slim\Routing\RouteContext;
use SallePW\SlimApp\Interfaces\UserRepository;
use SallePW\SlimApp\Interfaces\FriendRepository;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Utilities\SessionUtilities;
use SallePW\SlimApp\Utilities\ProfileUtilities;
use Slim\Views\Twig;
use SallePW\SlimApp\Model\FriendRequest;
use SallePW\SlimApp\Model\RegisteredUser;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class FriendController
{
    public function __construct(private Twig $twig, 
        private UserRepository $userRepository,
        private FriendRepository $friendRepository)
    {
    }
    
    public function showFriendList(Request $request, Response $response): Response
    {
        //if we don't have any active sessions, redirect back to the login
        if(SessionUtilities::getSession() === -1){
            SessionUtilities::setNotLoggedInError();
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $response
                    ->withHeader('Location', $routeParser->urlFor("login"))
                    ->withStatus(302);
            exit;
        }
        else{

            $user_id = SessionUtilities::getSession();
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $result = $this->friendRepository->getFriendsForUser($user_id);
            $friend_list = array();
            foreach($result as $request) {
                if((int)$request["friend_request_from_id"] !=  $user_id){
                     $req = new FriendRequest((int)$request["request_id"],(int)$request["friend_request_from_id"],(int)$request["friend_request_to_id"], (int)$request["accepted"]);
                     $user = $this->userRepository->getUsernameFromId((int)$request["friend_request_from_id"]);
                     $req->set_requester_username($user);
                }
                else{
                    $req = new FriendRequest((int)$request["request_id"],(int)$request["friend_request_to_id"], (int)$request["friend_request_from_id"], (int)$request["accepted"]);
                     $user = $this->userRepository->getUsernameFromId((int)$request["friend_request_to_id"]);
                     $req->set_requester_username($user);

                }
               
                $req->set_accept_date((String)$request["accept_date"]);
                array_push($friend_list, $req);
            } 
            return $this->twig->render(
                $response,
                'friend.twig',
                [
                    'formAction' => $routeParser->urlFor("friend-list"),
                    'formMethod' => "POST",
                    'all_friends' => $friend_list
                ]
            );
        }
    }
}