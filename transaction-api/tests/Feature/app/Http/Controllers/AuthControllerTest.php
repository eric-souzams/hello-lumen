<?php

namespace Feature\app\Http\Controllers;

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;

class AuthControllerTest extends \TestCase
{
    use DatabaseMigrations;

    public function testUserShouldNotAuthenticateWithWrongProvider()
    {
        // Prepare
        $payload = [
            'email' => 'eric@github.com',
            'password' => 'senha@123'
        ];

        // Act
        $response = $this->post(route('authenticate', ['provider' => 'google']), $payload);

        // Assert
        $response->assertResponseStatus(422);
        $response->seeJson(['erros' => ['main' => 'Wrong provider provided']]);
    }

    public function testUserShouldBeDeniedIfNotRegistered()
    {
        // Prepare
        $payload = [
            'email' => 'eric@github.com',
            'password' => 'senha@123'
        ];

        // Act
        $response = $this->post(route('authenticate', ['provider' => 'user']), $payload);

        // Assert
        $response->assertResponseStatus(401);
        $response->seeJson(['erros' => ['main' => 'Wrong credentials']]);
    }

    public function testUserShouldSendWrongPassword()
    {
        // Prepare
        $user = User::factory()->create(); 

        $payload = [
            'email' => $user->email,
            'password' => 'senha@1234'
        ];

        // Act
        $response = $this->post(route('authenticate', ['provider' => 'user']), $payload);

        // Assert
        $response->assertResponseStatus(401);
        $response->seeJson(['erros' => ['main' => 'Wrong credentials']]);
    }
}