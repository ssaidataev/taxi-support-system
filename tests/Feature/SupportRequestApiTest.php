<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SupportRequest;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SupportRequestApiTest extends TestCase
{
    use RefreshDatabase;

    // Создание пользователя для теста
    private function actingAsUser()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        return $this->actingAs($user);
    }

    // Тест: создание нового обращения
    // Тест: создание нового обращения
    public function test_create_support_request()
    {
        $user = $this->actingAsUser();

        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/support-requests', [
            'type' => 'problem',
            'description' => 'Description of the problem',
            'user_id' => $user->id,
            'order_id' => $order->id // Используем $order->id, а не $this->id
        ]);

        $response->assertStatus(201); // Проверяем, что запрос успешен
        $response->assertJsonStructure([ // Проверяем, что структура ответа правильная
            'id', 'type', 'description', 'user_id', 'order_id', 'created_at', 'updated_at'
        ]);
    }

// Тест: получение списка обращений
    public function test_get_support_requests()
    {
        $user = $this->actingAsUser();

        SupportRequest::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/support-requests');

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

// Тест: добавление сообщения в обращение
    public function test_add_message_to_support_request()
    {
        $user = $this->actingAsUser();

        $supportRequest = SupportRequest::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/support-requests/' . $supportRequest->id . '/messages', [
            'message' => 'Test message',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Test message',
            ]);
    }

// Тест: получение сообщений по обращению
    public function test_get_messages_for_support_request()
    {
        $user = $this->actingAsUser();

        $supportRequest = SupportRequest::factory()->create(['user_id' => $user->id]);
        $supportRequest->messages()->create([
            'user_id' => $user->id,
            'message' => 'Test message',
        ]);

        $response = $this->getJson('/api/support-requests/' . $supportRequest->id . '/messages');

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }
}
