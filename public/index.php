<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
//use GuzzleHttp\Client;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/dependencies.php';

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();

$app->addErrorMiddleware(true, false, false);

require_once __DIR__ . '/../config/routing.php';

$app->run();
