<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use DateTime;
use Exception;
use Slim\Routing\RouteContext;
use SallePW\SlimApp\Interfaces\UserRepository;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Utilities\SessionUtilities;
use Slim\Views\Twig;
use SallePW\SlimApp\Model\RegisteredUser;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SallePW\SlimApp\Utilities\ValidationHandler;

final class PasswordController
{
    public function __construct(private Twig $twig, 
        private UserRepository $userRepository,
       private ValidationHandler $validationHandler)
    {
    }
    
    public function showPasswordPage(Request $request, Response $response): Response
    {
        //if we don't have any active sessions, redirect back to the login
        if(empty($_SESSION)){
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $response
                    ->withHeader('Location', $routeParser->urlFor("login"))
                    ->withStatus(302);
            exit;
        }
        //otherwise, show the home page
        else{

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        
            return $this->twig->render(
                $response,
                'changePassword.twig',
                [
                    'formAction' => $routeParser->urlFor("handle-password-change"),
                    'formMethod' => "POST",
                ]
            );
        }
    }
      
    public function handlePasswordFormSubmission(Request $request, Response $response): Response
    {

        $data = $request->getParsedBody();

        $errors = [];

       //Check password requirements
        if (!$this->validationHandler->isPasswordValid($data['password_new']) || !$this->validationHandler->isPasswordValid($data['password_repeated'] )) {
            $errors['password_repeated'] = 'The password is not valid';
        }
        
        //Check passwords match
        if ($data['password_new'] !== $data['password_repeated']) {
            $errors['password_repeated'] = 'The passwords don\'t match';
        }
       
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        //get user via id
        //then, update the new password in the database

        $user_id = SessionUtilities::getSession();
        $password_ok = $this->userRepository->checkPasswordByID($user_id, $data['password']);
        if($password_ok != True || !empty($errors['password_repeated'])){

            $errors['password'] = 'The password isn\'t valid';
            return $this->twig->render(
                $response,
                'changePassword.twig',
                [
                    'formErrors' => $errors,
                    'formData' => $data,
                    'formAction' => $routeParser->urlFor("handle-password-change"),
                    'formMethod' => "POST",
                               
                ]
            );

        }else{
            $this->userRepository->updatePasswordByID($user_id, $data['password_new']);
            $errors['password_ok'] = 'Password changed successfully.';
            return $this->twig->render(
                $response,
                'changePassword.twig',
                [
                    'formErrors' => $errors,
                    'formData' => $data,
                    'formAction' => $routeParser->urlFor("handle-password-change"),
                    'formMethod' => "POST",
                               
                ]
            );
        }

    }
}
