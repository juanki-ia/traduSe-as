<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chat1 = Chat::create([
            'name' => 'Charla con Alan',
            'code' => 'chat1',
        ]);

        $chat2 = Chat::create([
            'name' => 'Charla con Jesus',
            'code' => 'chat2',
        ]);

        $chat1->users()->attach(1);
        $chat2->users()->attach(2);

    }
}
