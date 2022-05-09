<?php

declare(strict_types=1);

use SallePW\SlimApp\Controller\LoginUserController;
use SallePW\SlimApp\Controller\WalletController;
use SallePW\SlimApp\Controller\RegisterUserController;
use SallePW\SlimApp\Controller\SearchController;
use SallePW\SlimApp\Controller\ProfileController;
use SallePW\SlimApp\Controller\PasswordController;
use SallePW\SlimApp\Middleware\StartSessionMiddleware;
use SallePW\SlimApp\Controller\LandingController;
use SallePW\SlimApp\Controller\StoreController;
use SallePW\SlimApp\Controller\TokenController;
use SallePW\SlimApp\Controller\LogoutController;
use SallePW\SlimApp\Controller\WishlistController;
use SallePW\SlimApp\Controller\FriendController;
use SallePW\SlimApp\Controller\FriendRequestController;
use SallePW\SlimApp\Controller\FriendRequestSendController;

$app->add(StartSessionMiddleware::class);

//To redirect to register page
//$app->get('/', function ($req, $res, $args) {
    //return $res->withStatus(302)->withHeader('Location', '/');});

$app->get(
    '/',
    LandingController::class . ":showLandingPage"
)->setName('landingPage');

$app->get(
    '/store',
    StoreController::class . ":showStorePage"
)->setName('store');

$app->post(
    '/logout',
    LogoutController::class . ":logoutUser"
)->setName('logout');

$app->get(
    '/register',
    RegisterUserController::class . ":showRegisterForm"
)->setName('register');

$app->post(
    '/register',
    RegisterUserController::class . ":handleRegisterFormSubmission"
)->setName('handle-register');

$app->get(
    '/login',
    LoginUserController::class . ":showLoginForm"
)->setName('login');

$app->post(
    '/login',
    LoginUserController::class . ":handleLoginFormSubmission"
)->setName('handle-login');

$app->get(
    '/user/wallet',
    WalletController::class . ":showWalletForm"
)->setName('wallet');

$app->post(
    '/user/wallet',
    WalletController::class . ":handleWalletForm"
)->setName('handle-wallet');

$app->get(
    '/search',
    SearchController::class . ":showSearchForm"
)->setName('search');

$app->post(
    '/search',
    SearchController::class . ":handleSearch"
)->setName('handle-search');

$app->get(
    '/activate',
    TokenController::class . ":redeemToken"
)->setName('redeem-token');

$app->get(
    '/profile',
    profileController::class . ":showProfilePage"
)->setName('profile');

$app->post(
    '/profile',
    profileController::class . ":handleProfileFormSubmission"
)->setName('handle-profile');

$app->get(
    '/profile/changePassword',
    PasswordController::class . ":showPasswordPage"
)->setName('password');

$app->post(
    '/profile/changePassword',
    PasswordController::class . ":handlePasswordFormSubmission"
)->setName('handle-password-change');

$app->post(
    '/store/buy/{gameId}',
    StoreController::class . ":buyGame"
)->setName('buy-game');

$app->get(
    '/user/myGames',
    StoreController::class . ":myGames"
)->setName('my-games');

//Wishlist routing

$app->get(
    '/user/wishlist',
    WishlistController::class . ":showWishlist"
)->setName('show-wishlist');
/*
$app->get(
    '/user/wishlist/{gameId}',
    WishlistController::class . ":"
);*/


$app->post(
    '/user/wishlist/{gameId}',
    WishlistController::class . ":addGameToWishlist"
)->setName('add-game-wishlist');

$app->delete(
    '/user/wishlist/{gameId}',
    WishlistController::class . ":deleteGameFromWishlist"
)->setName('delete-wishlist');

$app->get(
    '/user/wishlist/{gameId}',
    WishlistController::class . ":displayGameDetails"
)->setNAme('game-details');

$app->get(
    '/user/friends',
    friendController::class . ":showFriendList"
)->setName('friend-list');

$app->get(
    '/user/friendRequests',
    friendRequestController::class . ":showFriendRequests"
)->setName('friend-requests');

$app->get(
    '/user/friendRequests/send',
    friendRequestSendController::class . ":showFriendRequestForm"
)->setName('friend-requests-send');

$app->post(
    '/user/friendRequests/send',
    friendRequestSendController::class . ":handleSendFriendRequest"
)->setName('handle-send-friend-request');

$app->get(
    '/user/friendRequests/accept/{requestId}',
    friendRequestController::class . ":acceptFriendGet"
)->setName('accept-friend-get');

$app->post(
    '/user/friendRequests/accept/{requestId}',
    friendRequestController::class . ":acceptFriend"
)->setName('accept-friend');


