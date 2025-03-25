<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Order;
use App\Models\SupportRequest;
use App\Models\Message;

class DatabaseSeeder extends Seeder
{
    /**
     * Запуск сидеров.
     *
     * @return void
     */
    public function run()
    {
        // Создаем 10 пользователей
//
        $this->call(OrderSeeder::class);
    }
}
