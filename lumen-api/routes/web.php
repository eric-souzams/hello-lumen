<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/todos', 'TodoController@getTodos');
$router->get('/todos/{todo}', 'TodoController@getTodo');
$router->post('/todos', 'TodoController@postTodo');
$router->post('/todos/{todo}/status/{status}', 'TodoController@postTodoStatus');
$router->delete('/todos/{todo}', 'TodoController@deleteTodo');
