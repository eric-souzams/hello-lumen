<?php

namespace Features\app\Http\Controllers;

use App\Models\Todo;
use Laravel\Lumen\Testing\DatabaseMigrations;

class TodoControllerTest extends \TestCase 
{
    use DatabaseMigrations;

    public function test_user_can_list_todos()
    {
        // Prepare
        Todo::factory(8)->create();

        // Act
        $response = $this->get('/todos');

        //Assert
        $response->assertResponseOk();
        $response->seeJsonStructure(['current_page']);
    }

    public function test_user_can_create_a_todo()
    {
        // Prepare
        $payload = [
            'title' => 'Tirar o Lixo',
            'description' => 'Não esquecer de tirar o lixo quinta as 14hs.'
        ];

        // Act
        $response = $this->post('/todos', $payload);

        //Assert
        $response->assertResponseStatus(201);

        $response->seeInDatabase('todos', $payload);
    }

    public function test_user_should_send_title_and_description()
    {
        // Prepare
        $payload = [
            'brave' => 'Tirar o Lixo'
        ];

        // Act
        $response = $this->post('/todos', $payload);

        //Assert
        $response->assertResponseStatus(422); // 422 - UNPROCESSABLE ENTITY
    }

    public function test_user_can_retrieve_a_specific_todo()
    {
        // Prepare
        $todo = Todo::factory()->create();

        // Act
        $uri = '/todos/' . $todo->id;
        $response = $this->get($uri);

        //Assert
        $response->assertResponseOk();
        $response->seeJson(['title' => $todo->title]);
    }

    public function test_user_should_receive_404_when_search_something_that_doesnt_exists()
    {
        // Prepare

        // Act
        $response = $this->get('/todos/1');

        //Assert
        $response->assertResponseStatus(404);
        $response->seeJsonContains(['error' => 'not found']);
    }

    public function test_user_can_delete_a_todo()
    {
        // Prepare
        $todo = Todo::factory()->create();

        // Act
        $uri = '/todos/' . $todo->id;
        $response = $this->delete($uri);

        //Assert
        $response->assertResponseStatus(204);

        $response->notSeeInDatabase('todos', [
            'id' => $todo->id
        ]);
    }

    public function test_user_can_set_todo_done()
    {
        // Prepare
        $todo = Todo::factory()->create();

        // Act
        $uri = '/todos/' . $todo->id . '/status/done';
        $response = $this->post($uri);

        //Assert
        $response->assertResponseStatus(200);
        $response->seeInDatabase('todos', [
            'id' => $todo->id,
            'done' => true
        ]);
    }

    public function test_user_can_set_todo_undone()
    {
        // Prepare
        $todo = Todo::factory()->create(['done' => true]);

        // Act
        $uri = '/todos/' . $todo->id . '/status/undone';
        $response = $this->post($uri);

        //Assert
        $response->assertResponseStatus(200);
        $response->seeInDatabase('todos', [
            'id' => $todo->id,
            'done' => false
        ]);
    }
}