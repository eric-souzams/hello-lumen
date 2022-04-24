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
        $response->seeJson(['errors' => ['main' => 'Wrong provider provided']]);
    }

    public function testUserShouldBeDeniedIfNotRegistered()
    {
        // Prepare
        $payload = [
            'email' => 'eric@github.com',
            'password' => 'senha@123'
        ];

        // Act
        $response = $this->post(route('authenticate', ['provider' => 'users']), $payload);

        // Assert
        $response->assertResponseStatus(401);
        $response->seeJson(['errors' => ['main' => 'Wrong credentials']]);
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
        $response = $this->post(route('authenticate', ['provider' => 'users']), $payload);

        // Assert
        $response->assertResponseStatus(401);
        $response->seeJson(['errors' => ['main' => 'Wrong credentials']]);
    }

    public function testUserCanAuthenticate()
    {
        // Prepare
        $this->artisan('passport:install');

        $user = User::factory()->create(); 

        $payload = [
            'email' => $user->email,
            'password' => 'senha@123'
        ];

        // Act
        $response = $this->post(route('authenticate', ['provider' => 'users']), $payload);

        // Assert
        $response->assertResponseStatus(200);
        $response->seeJsonStructure(['access_token', 'expires_at', 'provider']);
    }
}