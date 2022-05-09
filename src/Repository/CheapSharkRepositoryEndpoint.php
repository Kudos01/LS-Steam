<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Repository;

use GuzzleHttp as Guzzle;
use SallePW\SlimApp\Interfaces\CheapSharkRepository;

final class CheapSharkRepositoryEndpoint implements CheapSharkRepository{
    //This Repository uses Cheap Shark API
    // https://apidocs.cheapshark.com/

    public function getListOfStoreGames(): String {
        // Creating a Guzzle Client and getting list of the deals
        // The default response:
        // page size: 60
        //  page number: 0

        $requestUrl = $_ENV['CHEAP_SHARK_API'] . '/deals';

        $client = new Guzzle\Client();
        try {
            $res = $client->request('GET', $requestUrl);
        } catch (Guzzle\Exception\GuzzleException $e) {
            echo $e;
        }
        $test = (string) $res->getBody();
        //var_dump($test);
        return $test;
    }


    public function getGames(array $games_ids): array {
        $ids = '';

        foreach ($games_ids as $id){
            $ids = $ids . $id['game_shark_id'] . ',';
        }


        $requestUrl = $_ENV['CHEAP_SHARK_API'] . '/games?ids='.substr($ids, 0, -1);
        $client = new Guzzle\Client();

        try {
            $res = $client->request('GET', $requestUrl);
        } catch (Guzzle\Exception\GuzzleException $e) {
            echo $e;
        }

        //echo $res->getBody();
        //Decoding the result into array to display it later in the twig
        $result = json_decode((string)$res->getBody(), true);

        //var_dump($result);

        return $result;
    }
}