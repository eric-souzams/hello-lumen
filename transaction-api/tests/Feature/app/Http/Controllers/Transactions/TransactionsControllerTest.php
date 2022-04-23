<?php

namespace Feature\app\Http\Controllers;

use App\Models\Retailer;
use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;

class TransactionsControllerTest extends \TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testUserShouldNotSendWrongProvider() 
    {
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $payload = [
            'provider' => 'google',
            'payee_id' => 'fake_id',
            'amount'   =>  30
        ];

        $response = $this->actingAs($user, 'users')->post(route('postTransaction'), $payload);

        $response->assertResponseStatus(422);
    }
    
    public function testUserShouldBeExistingOnProviderToTransfer() 
    {
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $payload = [
            'provider' => 'user',
            'payee_id' => 'fake_id',
            'amount'   =>  30
        ];

        $response = $this->actingAs($user, 'users')->post(route('postTransaction'), $payload);

        $response->assertResponseStatus(404);
    }

    public function testUserShouldBeAValidUserToTransfer() 
    {
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $payload = [
            'provider' => 'user',
            'payee_id' => 'fake_id',
            'amount'   =>  30
        ];

        $response = $this->actingAs($user, 'users')->post(route('postTransaction'), $payload);

        $response->assertResponseStatus(404);
    }

    public function testRetailerShouldNotTransfer() 
    {
        $this->artisan('passport:install');

        $user = Retailer::factory()->create();
        
        $payload = [
            'provider' => 'user',
            'payee_id' => 'fake_id',
            'amount'   =>  30
        ];

        $response = $this->actingAs($user, 'retailers')->post(route('postTransaction'), $payload);

        $response->assertResponseStatus(401);
    }

    public function testUserShouldHaveMoneyToPerformSomeTransaction()
    {
        $this->artisan('passport:install');

        $userPayer = User::factory()->create();
        $userPayed = User::factory()->create();

        $payload = [
            'provider' => 'user',
            'payee_id' => $userPayed->id,
            'amount'   =>  30
        ];

        $response = $this->actingAs($userPayer, 'users')->post(route('postTransaction'), $payload);

        $response->assertResponseStatus(422);
    }
}