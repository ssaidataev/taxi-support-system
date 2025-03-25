<?php

namespace Database\Factories;

use App\Models\SupportRequest;
use App\Models\User;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupportRequestFactory extends Factory
{
    protected $model = SupportRequest::class;

    public function definition()
    {
        // Создаём пользователя
        $user = User::factory()->create();

        // Создаём заказ, связанный с этим пользователем
        $order = Order::factory()->create([
            'user_id' => $user->id,  // Связь с пользователем
        ]);

        // Возвращаем данные для создания обращения
        return [
            'type' => $this->faker->randomElement(['problem', 'question', 'complaint']),
            'description' => $this->faker->sentence,
            'user_id' => $user->id, // Связь с пользователем
            'order_id' => $order->id, // Связь с заказом
        ];
    }
}
