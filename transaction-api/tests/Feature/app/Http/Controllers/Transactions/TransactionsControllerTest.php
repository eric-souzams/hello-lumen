<?php

namespace Feature\app\Http\Controllers;

use App\Events\SendNotificationEvent;
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
            'provider' => 'users',
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
            'provider' => 'users',
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
            'provider' => 'users',
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
            'provider' => 'users',
            'payee_id' => $userPayed->id,
            'amount'   =>  30
        ];

        $response = $this->actingAs($userPayer, 'users')->post(route('postTransaction'), $payload);

        $response->assertResponseStatus(422);
    }

    public function testUserCanTransferMoney()
    {
        $this->expectsEvents(SendNotificationEvent::class);
        $this->artisan('passport:install');

        $userPayer = User::factory()->create();
        $userPayer->wallet->deposit(1000);

        $userPayed = User::factory()->create();

        $payload = [
            'provider' => 'users',
            'payee_id' => $userPayed->id,
            'amount'   =>  100
        ];

        $response = $this->actingAs($userPayer, 'users')->post(route('postTransaction'), $payload);

        $response->assertResponseStatus(200);
        
        $response->seeInDatabase('wallets', [
            'id' => $userPayer->wallet->id,
            'balance' => 900
        ]);

        $response->seeInDatabase('wallets', [
            'id' => $userPayed->wallet->id,
            'balance' => 100
        ]);
    }
}