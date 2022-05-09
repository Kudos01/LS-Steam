<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Repository;

use GuzzleHttp\Psr7\Query;
use PDO;
use SallePW\SlimApp\Model\Token;
use SallePW\SlimApp\Interfaces\TokenRepository;
use SallePW\SlimApp\Repository\PDOSingleton;

final class MySQLTokenRepository implements TokenRepository
{
    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }


    public function saveToken(Token $token): void
    {
        $query = <<< 'QUERY'
        INSERT INTO tokens (token_value, is_redeemed, user_id) 
        VALUES (:token_value, :is_redeemed, :user_id);
        QUERY;
        
        $statement = $this->database->connection()->prepare($query);

        $token_value = $token->getTokenValue();
        $is_redeemed = $token->isIsRedeemed();
        $user_id = $token->getUserId();

        $statement->bindParam('token_value',$token_value, PDO::PARAM_STR);
        $statement->bindParam('is_redeemed', $is_redeemed, PDO::PARAM_BOOL);
        $statement->bindParam('user_id', $user_id, PDO::PARAM_INT);

        $statement->execute();
    }

    public function redeemToken(Token $token): void
    {
        $query = <<< 'QUERY'
        UPDATE tokens SET is_redeemed = 1 WHERE token_id = :token_id;
QUERY;
        $statement = $this->database->connection()->prepare($query);

        $token_id = $token->getTokenId();
        $statement->bindParam('token_id', $token_id, PDO::PARAM_INT);
        $statement->execute();
    }

    public function getToken(string $token_value): ? Token
    {
        $query = <<< 'QUERY'
        SELECT * FROM tokens WHERE token_value = :token_value;
QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('token_value', $token_value, PDO::PARAM_STR);

        $statement->execute();

        //TODO check if the result is empty
        $result = $statement->fetchAll(PDO::FETCH_OBJ);
        if(sizeof($result) == 0){
            return null;
        }

        $token_obj = $result[0];

        $token_id = $token_obj->token_id;
        $is_redeemed = $token_obj->is_redeemed;
        $user_id = $token_obj->user_id;

        $token = new Token();
        $token->setTokenId(intval($token_id));
        $token->setTokenValue($token_value);
        $token->setIsRedeemed(boolval($is_redeemed));
        $token->setUserId(intval($user_id));

        return $token;
    }

    public function isTokenRedeemed(int $user_id): ? bool
    {
        $query = <<< 'QUERY'
        SELECT is_redeemed FROM tokens WHERE tokens.user_id = :user_id;
QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('user_id', $user_id, PDO::PARAM_INT);

        $statement->execute();

        $result = $statement->fetch();

        if(isset($result["is_redeemed"])){
            return (bool) $result["is_redeemed"];
        }
        return false;
    }

    public function getUserIdByToken(string $token_value) : int{
        $query = <<< 'QUERY'
        SELECT user_id FROM tokens WHERE tokens.token_value = :token_value;
QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('token_value', $token_value, PDO::PARAM_STR);

        $statement->execute();

        $result = $statement->fetch();

        if(isset($result["user_id"])){
            return (int) $result["user_id"];
        }
        return -1;
    }
}
