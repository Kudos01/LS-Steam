<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Repository;

use PDO;
use SallePW\SlimApp\Interfaces\UserGamesRepository;
use SallePW\SlimApp\Model\UserGame;
use SallePW\SlimApp\Repository\PDOSingleton;

final class MySQLUserGamesRepository implements UserGamesRepository
{
    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function save(UserGame $userGame) : void {
        $query = <<< 'QUERY'
        INSERT INTO user_games (game_shark_id, user_id) 
        VALUES (:game_shark_id, :user_id);
        QUERY;
        
        $statement = $this->database->connection()->prepare($query);
        
        $user_id = $userGame->getUserId();
        $game_shark_id = $userGame->getGameSharkId();

        $statement->bindParam('game_shark_id',$game_shark_id, PDO::PARAM_INT);
        $statement->bindParam('user_id', $user_id, PDO::PARAM_INT);

        $statement->execute();
    }

    public function getUserGamesIds(int $user_id): array {
        $query = <<< 'QUERY'
        SELECT game_shark_id FROM user_games WHERE user_id = :user_id;
        QUERY;
        
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('user_id', $user_id, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll();
    }
}
