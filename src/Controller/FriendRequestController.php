<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use DateTime;
use Exception;
use Ramsey\Uuid\Uuid;
use Slim\Routing\RouteContext;
use SallePW\SlimApp\Interfaces\UserRepository;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Utilities\SessionUtilities;
use SallePW\SlimApp\Utilities\ProfileUtilities;
use SallePW\SlimApp\Interfaces\FriendRepository;
use Slim\Views\Twig;
use SallePW\SlimApp\Model\RegisteredUser;
use SallePW\SlimApp\Model\FriendRequest;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class FriendRequestController
{
    public function __construct(private Twig $twig, 
        private UserRepository $userRepository,
        private FriendRepository $friendRepository)
    {
    }
    
    public function showFriendRequests(Request $request, Response $response): Response
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
            $errors = [];

            if(SessionUtilities::getRequestError() != null){
                $errors["friends"] = SessionUtilities::getRequestError();
                SessionUtilities::unsetRequestError();
            }

            $user_id = SessionUtilities::getSession();
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $result = $this->friendRepository->getFriendRequestForUser($user_id);
           
            $friend_requests = array();
            foreach($result as $request) {
                $req = new FriendRequest((int)$request["request_id"],(int)$request["friend_request_from_id"],(int)$request["friend_request_to_id"],(int)$request["accepted"]);
                $user = $this->userRepository->getUsernameFromId((int)$request["friend_request_from_id"]);
                $req->set_requester_username($user);
                array_push($friend_requests, $req);
            } 

            return $this->twig->render(
                $response,
                'friendRequests.twig',
                [
                    'formAction' => $routeParser->urlFor("friend-requests"),
                    'formMethod' => "POST",
                    'friend_requests' => $friend_requests,
                    'formErrors' => $errors
                ]
            );
        }
    
    }

    public function acceptFriendGet(Request $request, Response $response) : Response{
        SessionUtilities::setRequestError();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        return $response
                ->withHeader('Location', $routeParser->urlFor("friend-requests"))
                ->withStatus(302);
        exit;
    }

    public function acceptFriend(Request $request, Response $response) : Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $errors = [];
        $requestId =(int) $request->getAttribute('requestId');
        $userToId = $this->friendRepository->getToUserIdFromFriendRequest($requestId);

        if(SessionUtilities::getSession() == $userToId){
            $this->friendRepository->acceptFriendRequest($requestId);
            $this->friendRepository->setDateInDB($requestId);
        }else{
            SessionUtilities::setRequestError();
        }

        return $response
        ->withHeader('Location',  $routeParser->urlFor("friend-requests"))
        ->withStatus(302);
        exit;
    }

}
