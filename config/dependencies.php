<?php

use DI\Container;
use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Symfony\Component\Dotenv\Dotenv;

use SallePW\SlimApp\Controller\LoginUserController;
use SallePW\SlimApp\Controller\WalletController;
use SallePW\SlimApp\Controller\RegisterUserController;
use SallePW\SlimApp\Controller\LandingController;
use SallePW\SlimApp\Controller\StoreController;
use SallePW\SlimApp\Controller\TokenController;
use SallePW\SlimApp\Controller\LogoutController;
use SallePW\SlimApp\Controller\ProfileController;
use SallePW\SlimApp\Controller\WishlistController;
use SallePW\SlimApp\Controller\FriendController;
use SallePW\SlimApp\Controller\FriendRequestController;
use SallePW\SlimApp\Controller\FriendRequestSendController;
use SallePW\SlimApp\Controller\PasswordController;

use SallePW\SlimApp\Repository\MySQLUserRepository;
use SallePW\SlimApp\Repository\MySQLSearchRepository;
use SallePW\SlimApp\Repository\MySQLTokenRepository;
use SallePW\SlimApp\Repository\MySQLWalletRepository;
use SallePW\SlimApp\Repository\MySQLFriendRequestRepository;
use SallePW\SlimApp\Repository\MySQLWishlistRepository;
use SallePW\SlimApp\Repository\PDOSingleton;
use SallePW\SlimApp\Repository\CheapSharkRepositoryEndpoint;
use SallePW\SlimApp\Repository\CheapSharkEndpointDecorator;
use SallePW\SlimApp\Repository\MySQLUserGamesRepository;

use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\Search;
use SallePW\SlimApp\Model\SearchRepository;
use SallePW\SlimApp\Model\UserGames;
use SallePW\SlimApp\Model\FriendRequest;

use SallePW\SlimApp\Interfaces\TokenRepository;
use SallePW\SlimApp\Interfaces\UserGamesRepository;
use SallePW\SlimApp\Interfaces\UserRepository;
use SallePW\SlimApp\Interfaces\FriendRepository;
use SallePW\SlimApp\Interfaces\WishlistRepository;

use SallePW\SlimApp\Utilities\EmailHandler;
use Twig\Node\Expression\Binary\ConcatBinary;
use SallePW\SlimApp\Utilities\SessionUtilities;
use SallePW\SlimApp\Utilities\CacheUtilities;
use SallePW\SlimApp\Utilities\TwigSessionExtension;
use SallePW\SlimApp\Utilities\ProfileUtilities;
use SallePW\SlimApp\Utilities\LandingPageErrorUtilities;
use SallePW\SlimApp\Utilities\ValidationHandler;
use Slim\Flash\Messages;

$container = new Container();
$dotenv = new Dotenv();

define("DEFAULT_PICTURE",     "default_picture.png");
define("CACHE_FILE_NAME",     "cache.txt");

$dotenv->load(__DIR__ . '/../.env');

$container->set('db', function () {
    return PDOSingleton::getInstance(
        $_ENV['MYSQL_ROOT_USER'],
        $_ENV['MYSQL_ROOT_PASSWORD'],
        $_ENV['MYSQL_HOST'],
        $_ENV['MYSQL_PORT'],
        $_ENV['MYSQL_DATABASE']
    );
});

$container->set(
    //key
    'view',
    //callback
    function () {
        return Twig::create(__DIR__ . '/../templates', ['cache' => false, 'debug' => true]);
    }
);

$container->set(
    'flash',
    function () {
        return new Messages();
    }
);

$container->get('view')->getEnvironment()->addExtension(new \Twig\Extension\DebugExtension());

$container->get('view')->getEnvironment()->addGlobal(
    'session',
    new TwigSessionExtension()
);

$container->set(CacheUtilities::class, function (ContainerInterface $container){
    return new CacheUtilities();
});

$container->set(UserRepository::class, function (ContainerInterface $container) {
    return new MySQLUserRepository($container->get('db'));
});

$container->set(TokenRepository::class, function (ContainerInterface $container){
    return new MySQLTokenRepository($container->get('db'));
});
$container->set(FriendRepository::class, function (ContainerInterface $container){
    return new MySQLFriendRequestRepository($container->get('db'));
});
$container->set(WalletRepository::class, function (ContainerInterface $container) {
    return new MySQLWalletRepository($container->get('db'));
});

$container->set(UserGamesRepository::class, function (ContainerInterface $container){
    return new MySQLUserGamesRepository($container->get('db'));
});

$container->set(WishlistRepository::class, function (ContainerInterface $container){
    return new MySQLWishlistRepository($container->get('db'));
});


$container->set(LoginUserController::class, function (ContainerInterface $container) {
    return new LoginUserController(
        $container->get('view'), 
        $container->get(UserRepository::class),
        $container->get(ValidationHandler::class),
        $container->get(TokenRepository::class)
    );
});

$container->set(WishlistController::class, function (ContainerInterface $container) {
    return new WishlistController(
        $container->get('view'), 
        $container->get(WishlistRepository::class),
        $container->get(CheapSharkEndpointDecorator::class)
    );
});

$container->set(WalletController::class, function (ContainerInterface $container) {
    return new WalletController(
        $container->get('view'), 
        $container->get(UserRepository::class),
        $container->get(WalletRepository::class)
    );
});

$container->set(RegisterUserController::class, function (ContainerInterface $container) {
    return new RegisterUserController(
        $container->get('view'), 
        $container->get(UserRepository::class), 
        $container->get(TokenRepository::class), 
        $container->get(EmailHandler::class),
        $container->get(ValidationHandler::class)
    );
});

$container->set(LandingController::class, function (ContainerInterface $container){
    return new LandingController($container->get('view'));
});

$container->set(StoreController::class, function (ContainerInterface $container){
    return new StoreController(
        $container->get('view'), 
        $container->get(CheapSharkEndpointDecorator::class), 
        $container->get(UserGamesRepository::class),
        $container->get(WalletRepository::class),
        $container->get(WishlistRepository::class),
        $container->get('flash')
    );
});

$container->set(TokenController::class, function (ContainerInterface $container) {
    return new TokenController($container->get('view'), 
    $container->get(TokenRepository::class), 
    $container->get(EmailHandler::class), 
    $container->get(UserRepository::class), 
    $container->get(WalletRepository::class));
});

$container->set(LogoutController::class, function (ContainerInterface $container){
    return new LogoutController($container->get('view'));
});

$container->set(SessionUtilities::class, function (ContainerInterface $container){
    return new SessionUtilities();
});

$container->set(ProfileUtilities::class, function (ContainerInterface $container){
    return new ProfileUtilities(
        $container->get(UserRepository::class)
    );
});

$container->set(ProfileController::class, function (ContainerInterface $container) {
    return new ProfileController(
        $container->get('view'),
        $container->get(UserRepository::class)
    );
});

$container->set(PasswordController::class, function (ContainerInterface $container) {
    return new PasswordController(
        $container->get('view'),
        $container->get(UserRepository::class),
        $container->get(ValidationHandler::class)

    );
});

$container->set(FriendController::class, function (ContainerInterface $container) {
    return new FriendController(
        $container->get('view'),
        $container->get(UserRepository::class),
        $container->get(FriendRepository::class)
    );
});

$container->set(FriendRequestController::class, function (ContainerInterface $container) {
    return new FriendRequestController(
        $container->get('view'),
        $container->get(UserRepository::class),
        $container->get(FriendRepository::class)
    );
});

$container->set(FriendRequestSendController::class, function (ContainerInterface $container) {
    return new FriendRequestSendController(
        $container->get('view'),
        $container->get(UserRepository::class),
        $container->get(FriendRepository::class)
    );
});

$container->set(LandingPageErrorUtilities::class, function (ContainerInterface $container){
    return new LandingPageErrorUtilities();
});
/*
$app->post(
    '/user',
    CreateUserController::class . ":apply"
)->setName('create_user');
*/
