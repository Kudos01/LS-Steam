<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use DateTime;
//use Exception;
use Slim\Routing\RouteContext;
use SallePW\SlimApp\Model\Token;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\RegisteredUser;
use SallePW\SlimApp\Interfaces\UserRepository;
use SallePW\SlimApp\Interfaces\TokenRepository;
use SallePW\SlimApp\Utilities\SessionUtilities;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use SallePW\SlimApp\Utilities\EmailHandler;
use SallePW\SlimApp\Utilities\ValidationHandler;

final class RegisterUserController
{
    public function __construct(
        private Twig $twig, 
        private UserRepository $userRepository, 
        private TokenRepository $tokenRepository, 
        private EmailHandler $emailHandler,
        private ValidationHandler $validationHandler
    )
    {
        
    }

    public function showRegisterForm(Request $request, Response $response): Response
    {

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $this->twig->render(
            $response,
            'register.twig',
            [
                'formAction' => $routeParser->urlFor("handle-register"),
                'formMethod' => "POST",
            ]
        );
    }
    

    public function handleRegisterFormSubmission(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $errors = [];

        //Check email (has to be @salle.url.edu)
        if (!$this->validationHandler->validateEmail($data['email'])) {
            $errors['email'] = 'The email address is not valid';
        }
        
        //Check password requirements
        if (!$this->validationHandler->isPasswordValid($data['password'])) {
            $errors['password'] = 'The password is not valid';
        }
        
        //Check passwords match
        if ($data['password'] !== $data['password_repeated']) {
            $errors['password_repeated'] = 'The passwords don\'t match';
        }

        //Check age at least 18 years old
        // 31536000 is the number of seconds in a 365 days year.
        if (time() - strtotime($data['birthdate']) < 18 * 31536000) {
            $errors['birthdate'] = 'You are not old enough to register';
        }

        //username alphanumeric
        if (preg_match('~[0-9]~', $data['username']) === 0) {
            $errors['username'] = 'The username is not valid';
        }

        //Check email unique
        if($this->userRepository->checkUniqueEmail($data['email']) != -1){
            $errors['email_taken'] = 'The email is taken';
        }

        //Check if username is taken
        if($this->userRepository->checkUniqueUsername($data['username']) != -1){
            $errors['username_taken'] = 'The username is taken';
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if (empty($errors)) {

            // marking the token as unused

            $bday = new DateTime($data['birthdate']);

            // Create the user with the info given
            $userReg = new RegisteredUser(
                $data['username'] ?? '',
                $data['email'] ?? '',
                $data['password'] ?? '',
                $bday ?? '',
                $data['telephone'] ?? '',
                new DateTime(),
            );

            // add the user to the DB to the user table (necesary before building the token so that
            // the id of the user 
            $this->userRepository->save($userReg);

            //Should always evaluate to true but added the check just in case        
            $user_id = $this->userRepository->checkUniqueUsername($userReg->username());

            if($user_id !== -1){
                // Create the token (for now using username as the token value)
                $token = new Token(
                    $userReg->username() ?? '',
                    false,
                    //Pass the ID of the user we just saved to the DB
                    $user_id
                );

                $token->setUserId($user_id);
                $token->setIsredeemed(false);
                $token->setTokenValue($data['username']);

                //var_dump($token);

                // and to the token table
                $this->tokenRepository->saveToken($token);

                // FOR TESTING, REMOVE
                // $this->walletRepository->addDefaultAmount(SessionUtilities::getSession());

                //Send an activation token email
                $this->emailHandler->sendActivationToken($userReg, $token);

                return $this->twig->render(
                    $response,
                    'registrationSuccess.twig'
                );
            }else{
                //Send error that the registration was unsuccesful, contact support (?)
                $errors['token'] = "Registration Unsuccessful. Please contact support";
            }
        }

        return $this->twig->render(
            $response,
            'register.twig',
            [
                'formErrors' => $errors,
                'formData' => $data,
                'formAction' => $routeParser->urlFor("handle-register"),
                'formMethod' => "POST",
            ]
        );
    

    }
    
}
