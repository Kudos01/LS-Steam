<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Repository;

use PDO;
use SallePW\SlimApp\Interfaces\WishlistRepository;
use SallePW\SlimApp\Repository\PDOSingleton;

final class MySQLWishlistRepository implements WishlistRepository
{
    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function addGameToWishlist($game_shark_id, $user_id): void
    {
        $query = <<<'QUERY'
        INSERT INTO wishlist(game_shark_id, user_id) VALUES(:game_shark_id, :user_id)
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('user_id', $user_id, PDO::PARAM_INT);
        $statement->bindParam('game_shark_id', $game_shark_id, PDO::PARAM_INT);

        $statement->execute();
    }

    public function deleteGameFromWishlist($game_shark_id, $user_id): void 
    {
        $query = <<<'QUERY'
        DELETE FROM wishlist WHERE game_shark_id = :game_shark_id AND user_id = :user_id
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('user_id', $user_id, PDO::PARAM_INT);
        $statement->bindParam('game_shark_id', $game_shark_id, PDO::PARAM_INT);

        $statement->execute();
    }

    public function getWishlistGamesIds($user_id): array
    {
        $query = <<<'QUERY'
        SELECT game_shark_id from wishlist WHERE user_id = :user_id
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('user_id', $user_id, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll();
    }

    public function checkIfTheGameIsOnTheList($game_shark_id, $user_id): bool
    {
        $query = <<<'QUERY'
        SELECT game_shark_id from wishlist WHERE game_shark_id = :game_shark_id AND user_id = :user_id
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('user_id', $user_id, PDO::PARAM_INT);
        $statement->bindParam('game_shark_id', $game_shark_id, PDO::PARAM_INT);

        $statement->execute();

        $result = $statement -> fetch();

        if(isset($result['game_shark_id'])){
            return true;
        }

        return false;
    }
}