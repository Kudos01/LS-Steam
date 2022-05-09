<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use DateTime;
use Exception;
use Slim\Routing\RouteContext;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Interfaces\UserRepository;
use SallePW\SlimApp\Interfaces\TokenRepository;
use SallePW\SlimApp\Utilities\SessionUtilities;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SallePW\SlimApp\Utilities\ValidationHandler;

final class LoginUserController
{
    public function __construct(
        private Twig $twig, 
        private UserRepository $userRepository, 
        private ValidationHandler $validationHandler,
        private TokenRepository $tokenRepository
        )
    {
    }
    
    public function showLoginForm(Request $request, Response $response): Response
    {
        $errors = [];

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if(null === SessionUtilities::getNotLoggedInError()){
            return $this->twig->render(
                $response,
                'login.twig',
                [
                    'formAction' => $routeParser->urlFor("handle-login"),
                    'formMethod' => "POST",
                    
                ]
            );
        }else{            
            $errors['not_logged_in'] = SessionUtilities::getNotLoggedInError();
            SessionUtilities::unsetNotLoggedInError();

            return $this->twig->render(
                $response,
                'login.twig',
                [
                    'formErrors' => $errors,
                    'formAction' => $routeParser->urlFor("handle-login"),
                    'formMethod' => "POST",
                    
                ]
            );
        }

    }

    
    public function handleLoginFormSubmission(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $errors = [];

        //Check email validation (has to be @salle.url.edu)
         if (!$this->validationHandler->validateEmail($data['email']) || empty($data['email'])) {
            $errors['email'] = 'The email isn\'t valid';
        }
        else if (!$this->validationHandler->isPasswordValid($data['password'])) {
            $errors['email'] = 'The login isn\'t valid';
        }
       
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        try {
            $user = new User(
                $data['email'] ?? '',
                $data['password'] ?? '',
                new DateTime()
            );
            //check if the user is in the db and if the password is right
            $user_id = $this->userRepository->check($user);     

        } catch (Exception $exception) {
            // You could render a .twig template here to show the error
            $response->getBody()
                ->write('Unexpected error: ' . $exception->getMessage());
            return $response->withStatus(500);
        }

        //Check the result and show an error if the login is incorrect

        if($user_id === -1){
            $errors['email'] = 'The login isn\'t valid';
            return $this->twig->render(
                $response,
                'login.twig',
                [
                    'formErrors' => $errors,
                    'formData' => $data,
                    'formAction' => $routeParser->urlFor("handle-login"),
                    'formMethod' => "POST",
                ]
            );

        }else{
            
            $isTokenRedeemed = $this->tokenRepository->isTokenRedeemed($user_id);

            if($isTokenRedeemed){
                //if the password and user are correct, start the user session with their id
                SessionUtilities::setSession($user_id);
                SessionUtilities::setPicture($this->userRepository->getImageByUserId($user_id));

                $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                return $response
                        ->withHeader('Location', $routeParser->urlFor("store"))
                        ->withStatus(302);
                exit;
            }
            else {
                $errors['email'] = 'Pleaase activate your account';
                
                return $this->twig->render(
                    $response,
                    'login.twig',
                    [
                        'formErrors' => $errors,
                        'formData' => $data,
                        'formAction' => $routeParser->urlFor("handle-login"),
                        'formMethod' => "POST",
                                   
                    ]
                );
    
            }
            
        }


    }

}
