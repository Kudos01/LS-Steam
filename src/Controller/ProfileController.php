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
use Slim\Views\Twig;
use SallePW\SlimApp\Model\RegisteredUser;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class ProfileController
{
    public function __construct(private Twig $twig, 
        private UserRepository $userRepository)
    {
    }
    
    public function showProfilePage(Request $request, Response $response): Response
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
            $date = new DateTime();
            $ru = new RegisteredUser("0","0","0", $date,"0",$date);
            $ru = $this->userRepository->getUserByID($user_id);
            $converted_date = date_format($ru->birthdate(), 'Y-m-d');

            
            //create an object to store a bunch of information we need 
             //get user information
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        

        return $this->twig->render(
            $response,
            'profile.twig',
            [
                'formAction' => $routeParser->urlFor("handle-profile"),
                'formMethod' => "POST",
                'username' => $ru->username(),
                'email' => $ru->email(),
                'birthdate' => $converted_date,
                'phone' => $ru->phone_number()
            ]
        );
        }
    }


    public function handleProfileFormSubmission(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $errors = [];
        $uuid = strval(Uuid::uuid4());
        $MAX_FILE_SIZE = 500000 * 2;
        $MAX_DIMENSIONS_SIZE = 500;
        $user_id = SessionUtilities::getSession();
        $date = new DateTime();
        $ru = new RegisteredUser("0","0","0", $date,"0",$date);
        $ru = $this->userRepository->getUserByID($user_id);
        $converted_date = date_format($ru->birthdate(), 'Y-m-d');
        $uploadOk = 1;

        if(!empty($_FILES["profile_pic"]["name"])){
            $target_dir = "../public/uploads/";
            $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            $final_dest = $target_dir . $uuid . '.' . $imageFileType;
            // Check if image file is a actual image or fake image

        //validate image
            $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
            if($check === false) {
                $errors['profile_pic'] = "File is not an image.";
                $uploadOk = 0;
            }else if($_FILES["profile_pic"]['size'] > $MAX_FILE_SIZE){
                $errors['profile_pic'] = "File size too large. It must be less than 1Mb ";
                $uploadOk = 0;
            }else if($check[0] > $MAX_DIMENSIONS_SIZE || $check[1] > $MAX_DIMENSIONS_SIZE){
                $errors['profile_pic'] = "Files must be 500x500 ";
                $uploadOk = 0;
            }else if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"){
                $errors['profile_pic'] = "File must be .png or .jpg";
                $uploadOk = 0;
            }else{
                $uploadOk = 1;
            }
        }

        //TODO: consider if we need an actual phone number error to take into consideration
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        //Check the result and show an error if the login is incorrect

        if($uploadOk === 0){
            return $this->twig->render(
                $response,
                'profile.twig',
                [
                    'formErrors' => $errors,
                    'formData' => $data,
                    'formAction' => $routeParser->urlFor("handle-profile"),
                    'formMethod' => "POST",
                    'username' => $ru->username(),
                    'email' => $ru->email(),
                    'birthdate' => $converted_date,
                    'phone' => $ru->phone_number(),
                       
                ]
            );

        }else{

            if(!empty($_FILES["profile_pic"]["name"])){

                move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $final_dest);
                $image_to_remove = $this->userRepository->getImageByUserId($user_id);

                $picture = $uuid . '.' . $imageFileType;
                
                if($image_to_remove != DEFAULT_PICTURE){

                    $path = $target_dir . $image_to_remove;

                    if(unlink($path) == false){
                        $errors['profile_pic'] = "Could not update profile photo.";
                    }
                    else{
                        $this->userRepository->updateProfilePictureByID($user_id, $picture);      
                        $errors['all_ok'] = 'Information Changed Successfully';
                        SessionUtilities::setPicture($picture);
                    }   

                }else{
                    $this->userRepository->updateProfilePictureByID($user_id, $picture);      
                    $errors['all_ok'] = 'Information Changed Successfully';
                    SessionUtilities::setPicture($picture);
                }
            }

            if(!empty($data["telephone"])){
                $this->userRepository->updateTelephoneByID($user_id, $data["telephone"]);
                $errors['all_ok'] = 'Information Changed Successfully';
            }

            return $this->twig->render(
                $response,
                'profile.twig',
                [
                    'formErrors' => $errors,
                    'formData' => $data,
                    'formAction' => $routeParser->urlFor("handle-profile"),
                    'formMethod' => "POST",
                    'username' => $ru->username(),
                    'email' => $ru->email(),
                    'birthdate' => $converted_date,
                    'phone' => $data["telephone"],
                    
                ]
            );
        }
    }
}
