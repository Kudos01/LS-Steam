<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Repository;

use PDO;
use DateTime;
use SallePW\SlimApp\Model\FriendRequest;
use SallePW\SlimApp\Repository\PDOSingleton;
use SallePW\SlimApp\Interfaces\FriendRepository;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\RegisteredUser;
use SallePW\SlimApp\Interfaces\UserRepository;


final class MySQLFriendRequestRepository implements FriendRepository
{
    
    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
        
    }


    public function createNewFriendRequest(int $friend_request_from_id, int $friend_request_to_id): void
    {
        
        $query = <<< 'QUERY'
        INSERT INTO friend_requests (friend_request_from_id, friend_request_to_id, accepted) 
        VALUES (:friend_request_from_id, :friend_request_to_id, :accepted);
        QUERY;
        
        $statement = $this->database->connection()->prepare($query);
        $accepted = 3;
        //0 = no friend request
        //1 = friends
        //2 = declined
        //3 = friend request pending
        $statement->bindParam('friend_request_from_id',$friend_request_from_id, PDO::PARAM_INT);
        $statement->bindParam('friend_request_to_id', $friend_request_to_id, PDO::PARAM_INT);
        $statement->bindParam('accepted', $accepted, PDO::PARAM_STR);

        $statement->execute();
    }

    public function getFriendRequestForUser(int $friend_request_to_id): array
    {
        $query = <<< 'QUERY'
        SELECT * FROM friend_requests WHERE friend_requests.friend_request_to_id = :friend_request_to_id AND friend_requests.accepted = 3 
        QUERY;
        
        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('friend_request_to_id', $friend_request_to_id, PDO::PARAM_INT);

        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);
        return $result;
    }

    public function getFriendsForUser(int $friend_request_to_id): array
    {
        $query = <<< 'QUERY'
        SELECT * FROM friend_requests WHERE (friend_requests.friend_request_to_id = :friend_request_to_id OR friend_requests.friend_request_from_id = :friend_request_to_id) AND friend_requests.accepted = 1 
        QUERY;
        
        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('friend_request_to_id', $friend_request_to_id, PDO::PARAM_INT);

        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getRequestIdFromUsers(int $friend_request_from_id, int $friend_request_to_id): int
    {
        $query = <<< 'QUERY'
        SELECT request_id FROM friend_requests WHERE friend_request_to_id = :friend_request_to_id AND friend_request_from_id = :friend_request_from_id 
        QUERY;
        //echo($friend_request_from_id);
        //echo($friend_request_to_id);
        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('friend_request_from_id',$friend_request_from_id, PDO::PARAM_INT);
        $statement->bindParam('friend_request_to_id', $friend_request_to_id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch();
        return (int) $result['request_id'];
    }
    
    public function getToUserIdFromFriendRequest(int $request_id): int
    {
        $query = <<< 'QUERY'
        SELECT friend_request_to_id FROM friend_requests WHERE request_id = :request_id
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('request_id',$request_id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch();
        if(is_null($result)){
            return -1;
        } 
        return (int) $result['friend_request_to_id'];
    }

    public function acceptFriendRequest(int $request_id): void
    {
        $query = <<< 'QUERY'
        UPDATE friend_requests SET accepted = :accepted WHERE request_id = :request_id
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $accepted = 1;
        $statement->bindParam('accepted', $accepted, PDO::PARAM_INT);
        $statement->bindParam('request_id',$request_id, PDO::PARAM_INT);

        $statement->execute();
        
    }
    public function setDateInDB(int $request_id): void
    {
        $query = <<< 'QUERY'
        UPDATE friend_requests SET accept_date = :accept_date WHERE request_id = :request_id
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $accept_date = date("Y/m/d");
        $statement->bindParam('request_id',$request_id, PDO::PARAM_INT);
        $statement->bindParam('accept_date', $accept_date, PDO::PARAM_STR);        

        $statement->execute();
        
    }
    public function checkIfFriendAlreadyRequested(int $friend_request_from_id, int $friend_request_to_id): bool
    {
        $query = <<< 'QUERY'
        SELECT accepted FROM friend_requests WHERE (friend_request_to_id = :friend_request_to_id AND friend_request_from_id = :friend_request_from_id) OR (friend_request_to_id = :friend_request_from_id AND friend_request_to_id = :friend_request_from_id)
        QUERY;
        
        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('friend_request_from_id',$friend_request_from_id, PDO::PARAM_INT);
        $statement->bindParam('friend_request_to_id', $friend_request_to_id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch();
        if(is_null($result)){
             return FALSE;
        }
        else if($result != FALSE){
            return TRUE;
        }
        else{
            return FALSE;
        }
    }

}
