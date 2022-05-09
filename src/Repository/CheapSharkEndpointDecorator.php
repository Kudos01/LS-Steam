<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Repository;
use SallePW\SlimApp\Utilities\CacheUtilities;

use GuzzleHttp as Guzzle;
use SallePW\SlimApp\Interfaces\CheapSharkRepository;

final class CheapSharkEndpointDecorator implements CheapSharkRepository{
    //This Repository uses Cheap Shark API
    // https://apidocs.cheapshark.com/

    public function __construct(
        private CheapSharkRepositoryEndpoint $cheapSharkRepo,
        private CacheUtilities $cacheUtils)
    {
    }

    public function getListOfStoreGames(): string {
        // Creating a Guzzle Client and getting list of the deals
        // The default response:
        // page size: 60
        //  page number: 0

        if($this->cacheUtils->isempty()){
            //fill the cache and return api call
            $games = $this->cheapSharkRepo->getListOfStoreGames();
            $this->cacheUtils->fillCache($games);
            return $games;
        }else{
            //get and return from the cache
            return $this->cacheUtils->getCache();
        }
        
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