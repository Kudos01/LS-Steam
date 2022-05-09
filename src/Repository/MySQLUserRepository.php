<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Repository;

use PDO;
use DateTime;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\RegisteredUser;
use SallePW\SlimApp\Interfaces\UserRepository;
use SallePW\SlimApp\Repository\PDOSingleton;

final class MysqlUserRepository implements UserRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }
    /*
    public function save(User $user): void
    {
        $query = <<<'QUERY'
        INSERT INTO users(email, password, created_at)
        VALUES(:email, :password, :created_at)
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $email = $user->email();
        $password = $user->password();
        $createdAt = $user->createdAt()->format(self::DATE_FORMAT);
        //$updatedAt = $user->updatedAt()->format(self::DATE_FORMAT);

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('created_at', $createdAt, PDO::PARAM_STR);
        //$statement->bindParam('updated_at', $updatedAt, PDO::PARAM_STR);

        $statement->execute();
    }
    */

    public function save(RegisteredUser $user): void
    {
        $query = <<<'QUERY'
        INSERT INTO users(username, email, hashed_password, birthdate, phone, created_at, profile_uuid)
        VALUES(:username, :email, :hashed_password, :birthdate, :phone, :created_at, :profile_uuid)
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $username = $user->username();
        $email = $user->email();
        // hash the passowrd before storing
        $hashed_password = password_hash($user->password(), PASSWORD_DEFAULT);
        $birthdate = $user->birthdate()->format(self::DATE_FORMAT);
        $createdAt = $user->createdAt()->format(self::DATE_FORMAT);
        $phone = $user->phone_number();
        $profile_uuid = $user->profile_uuid();
        

        $statement->bindParam('username', $username, PDO::PARAM_STR);
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('hashed_password', $hashed_password, PDO::PARAM_STR);
        $statement->bindParam('birthdate', $birthdate, PDO::PARAM_STR);
        $statement->bindParam('phone', $phone, PDO::PARAM_STR);
        $statement->bindParam('created_at', $createdAt, PDO::PARAM_STR);
        $statement->bindParam('profile_uuid', $profile_uuid, PDO::PARAM_STR);

        $statement->execute();
    }

    private function checkEmail($email) : bool {
        $find1 = strpos($email, '@');
        return $find1 > 0;
    }

    public function check(User $user): int
    {
        if($this->checkEmail($user->login())){
            $query = <<<'QUERY'
                Select user_id, hashed_password from users WHERE users.email = :email
            QUERY;

            $statement = $this->database->connection()->prepare($query);

            $email = $user->login();
            $statement->bindParam('email', $email, PDO::PARAM_STR);

            $statement->execute();
            
            $result = $statement->fetch();
            //var_dump($result);
            if(isset($result["user_id"]) && (password_verify($user->password(), $result["hashed_password"])) ){
                return (int) $result["user_id"];
            }else{
                return -1;
            }
        }else{
             $query = <<<'QUERY'
                Select user_id, hashed_password from users WHERE users.username = :username
            QUERY;

            $statement = $this->database->connection()->prepare($query);

            $username = $user->login();
            $statement->bindParam('username', $username, PDO::PARAM_STR);

            $statement->execute();
            
            $result = $statement->fetch();

            if(isset($result["user_id"]) && (password_verify($user->password(), $result["hashed_password"])) ){
                return (int) $result["user_id"];
            }else{
                return -1;
            }

        }
        
    }

    //todo: replace user->email with
    public function getUserByID(int $user_id): RegisteredUser
    {
            $query = <<<'QUERY'
                Select username, email, birthdate, phone from users WHERE users.user_id = :user_id
            QUERY;

            $statement = $this->database->connection()->prepare($query);
            $statement->bindParam('user_id', $user_id, PDO::PARAM_STR);

            $statement->execute();
            
            $result = $statement->fetch();

            $date = new DateTime($result["birthdate"]);

            $ru = new RegisteredUser("0","0","0", $date,"0",$date);
            $ru->setUsername($result["username"]);
            $ru->setEmail($result["email"]);
            $ru->setBirthdate($date);
            $ru->setPhone($result["phone"]);

           return $ru;
    }

    public function checkPasswordByID(int $user_id, String $input_password) : bool {
        $query = <<<'QUERY'
        Select hashed_password from users WHERE users.user_id = :user_id
        QUERY;

        $statement = $this->database->connection()->prepare($query);

         $statement->bindParam('user_id', $user_id, PDO::PARAM_STR);

        $statement->execute();

        $result = $statement -> fetch();

        return (password_verify($input_password, $result["hashed_password"]));

    }
    public function updatePasswordByID(int $user_id, String $new_password_no_hash) : void 
    {
        $query = <<<'QUERY'
        UPDATE users SET hashed_password= :new_password WHERE users.user_id = :user_id
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $new_password = password_hash($new_password_no_hash, PASSWORD_DEFAULT);
        $statement->bindParam('new_password', $new_password, PDO::PARAM_STR);

        $statement->execute();
    }

    public function getUserEmailByToken(String $token) : string
    {
        $query = <<<'QUERY'
        Select user_id from tokens WHERE token_value = :token_value
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('token_value', $token, PDO::PARAM_STR);

        $statement->execute();

        $result = $statement -> fetch();
    
        if(isset($result["user_id"])){
            $query_get_user_email = <<<'QUERY'
            Select email from users WHERE user_id = :user_id
            QUERY;

            $statement = $this->database->connection()->prepare($query_get_user_email);

            $statement->bindParam('user_id', $result["user_id"], PDO::PARAM_INT);
    
            $statement->execute();

            $user_result = $statement->fetch();

            return $user_result['email'];
        }

        return "";

    }

    public function checkUniqueUsername(String $username): int
    {
        $query = <<<'QUERY'
        Select user_id from users WHERE users.username = :username
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('username', $username, PDO::PARAM_STR);

        $statement->execute();
        
        $result = $statement->fetch();

        if(isset($result["user_id"])){
            return (int) $result["user_id"];
        }
        return -1;
    }

    public function getUserByUsername(String $username): int
    {
        $query = <<<'QUERY'
        Select user_id from users WHERE users.username = :username
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('username', $username, PDO::PARAM_STR);

        $statement->execute();
        
        $result = $statement->fetch();
        return (int) $result['user_id'];
    }

    public function getImageByUserId(int $user_id): String
    {
        $query = <<<'QUERY'
        Select profile_uuid from users WHERE users.user_id = :user_id
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('user_id', $user_id, PDO::PARAM_STR);

        $statement->execute();
        
        $result = $statement->fetch();

        if(isset($result["profile_uuid"])){
            return $result["profile_uuid"];
        }
        return -1;
    }

    public function checkUniqueEmail(String $email): int
    {
        $query = <<<'QUERY'
        Select user_id from users WHERE users.email = :email
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);

        $statement->execute();
        
        $result = $statement->fetch();

        if(isset($result["user_id"]) == 1){
            return (int) $result["user_id"];
        }
        return -1;
    }

    public function updateProfilePictureByID(int $user_id, String $profile_uuid): void {
        $query = <<<'QUERY'
        UPDATE users SET profile_uuid= :profile_uuid WHERE users.user_id = :user_id
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $statement->bindParam('profile_uuid', $profile_uuid, PDO::PARAM_STR);

        $statement->execute();
    }

    public function updateTelephoneByID(int $user_id, String $phone): void {
        $query = <<<'QUERY'
        UPDATE users SET phone= :phone WHERE users.user_id = :user_id
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $statement->bindParam('phone', $phone, PDO::PARAM_STR);

        $statement->execute();
    }
     public function getUsernameFromId(int $user_id) : String {

        $query = <<<'QUERY'
        Select username from users WHERE users.user_id = :user_id LIMIT 1
        QUERY;

        $statement = $this->database->connection()->prepare($query);

         $statement->bindParam('user_id', $user_id, PDO::PARAM_STR);

        $statement->execute();
        //TODO: FIX UP THIS!!!!!!
        $result = $statement -> fetchAll(PDO::FETCH_ASSOC);
        return $result[0]["username"];

    }
}
