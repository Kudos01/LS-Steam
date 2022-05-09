<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Repository;

use GuzzleHttp\Psr7\Query;
use PDO;
use SallePW\SlimApp\Model\Money;
use SallePW\SlimApp\Interfaces\WalletRepository;
use SallePW\SlimApp\Repository\PDOSingleton;

define("DEFAULT_MONEY_AMOUNT", 50.00);

final class MySQLWalletRepository implements WalletRepository
{
    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    //returns the money 
    public function getUserBalanace(int $user_id): float
    {
        $query = <<<'QUERY'
        Select amount from money WHERE money.user_id = :user_id
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('user_id', $user_id, PDO::PARAM_STR);

        $statement->execute();
        
        $result = $statement->fetch();

        if(isset($result["amount"])){
            return (float) $result["amount"];
        }
        //means no user with that ID
        return -1;
    }

    public function addAmount(int $user_id, float $amount): void
    {
        $query = <<< 'QUERY'
        UPDATE money SET amount = amount + :amount WHERE user_id = :user_id;
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $statement->bindParam('amount', $amount, PDO::PARAM_STR);
        $statement->execute();
    }

    public function addDefaultAmount(int $user_id): void
    {
        $query = <<<'QUERY'
        INSERT INTO money(amount, user_id)
        VALUES(:amount, :user_id)
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $DEFAULT_MONEY_AMOUNT = DEFAULT_MONEY_AMOUNT;
        $statement->bindParam('amount', $DEFAULT_MONEY_AMOUNT, PDO::PARAM_STR);

        $statement->execute();
    }

    public function removeAmount(int $user_id, float $amount): void
    {
        $query = <<< 'QUERY'
        UPDATE money SET amount = amount - :amount WHERE user_id = :user_id;
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $statement->bindParam('amount', $amount, PDO::PARAM_STR);        
        $statement->execute();
    }
}
