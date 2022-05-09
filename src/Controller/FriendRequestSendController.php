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
use SallePW\SlimApp\Model\RegisteredUser;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class FriendRequestSendController
{
    public function __construct(private Twig $twig, 
        private UserRepository $userRepository,
        private FriendRepository $friendRepository)
    {
    }
    
    public function showFriendRequestForm(Request $request, Response $response): Response
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

            return $this->twig->render(
                $response,
                'sendFriendRequest.twig',
                [
                    'formAction' => $routeParser->urlFor("friend-requests-send"),
                    'formMethod' => "POST"
                ]
            );
        }
    }

    public function handleSendFriendRequest(Request $request, Response $response): Response
    {       
        $data = $request->getParsedBody();
        $user_id = SessionUtilities::getSession();
        $errors = [];
        $other_user = @$this->userRepository->getUserByUsername($data['username']);
        $ru = $this->userRepository->getUserByID($user_id);
        

        //Check if username is taken
        if (preg_match('~[0-9]~', $data['username']) === 0) {
            $errors['username'] = 'The username is not valid';
        }
        else if ($data['username'] === $ru->username()) {
            $errors['username'] = 'You can\'t be friends with yourself!';
        }
        else if($this->userRepository->checkUniqueUsername($data['username']) == -1){
            $errors['username'] = 'You can\'t be friends with a user that does not exist';
        }
        else if($this->friendRepository->checkIfFriendAlreadyRequested($user_id, $other_user) == TRUE){
            $errors['username'] = 'Error, already friends, or request was not accepted';
        }
    
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        if (empty($errors)) {
           
            $this->friendRepository->createNewFriendRequest($user_id ,$other_user);
                return $this->twig->render(
                    $response,
                    'sendFriendRequest.twig',
                    [
                        'success' => "The request has been sent succesfully!",
                        'formData' => $data,
                        'formAction' => $routeParser->urlFor("handle-send-friend-request"),
                        'formMethod' => "POST"
                    ]
                );
            }else{
                return $this->twig->render(
                    $response,
                    'sendFriendRequest.twig',
                    [
                        'formErrors' => $errors,
                        'formData' => $data,
                        'formAction' => $routeParser->urlFor("handle-send-friend-request"),
                        'formMethod' => "POST"
                    ]
                );
            }
    }
}
