<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Repository;

use PDO;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\Search;
use SallePW\SlimApp\Model\SearchRepository;
use SallePW\SlimApp\Repository\PDOSingleton;

final class MySQLSearchRepository implements SearchRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function save(Search $search): void
    {
        $query = <<<'QUERY'
        INSERT INTO Search(user_id, search, created_at)
        VALUES(:user_id, :search_name, :created_at)
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $user_id = $search->user_id();
        $search_name = $search->search();
        $createdAt = $search->createdAt()->format(self::DATE_FORMAT);

        $statement->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $statement->bindParam('search_name', $search_name, PDO::PARAM_STR);
        $statement->bindParam('created_at', $createdAt, PDO::PARAM_STR);

        $statement->execute();
    }
}
