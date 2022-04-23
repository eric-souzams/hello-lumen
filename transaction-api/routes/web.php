<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Models\User;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// new user
$router->get('/', function() {User::factory()->create(['email' => 'eric@github.com']);});

// authentication
$router->post('/auth/{provider}', ['as' => 'authenticate', 'uses' => 'AuthController@postAuthenticate']);

// user
$router->get('/users/me', ['as' => 'usersMe', 'uses' => 'MeController@getMe']);

$router->post('/transactions', ['as' => 'postTransaction', 'uses' => 'Transactions\TransactionsController@postTransaction']);