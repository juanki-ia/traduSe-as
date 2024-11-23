<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Jesus Jaldin',
            'email' => 'jesus@gmail.com',
            'password' => bcrypt('12345678'),
        ]);

        User::create([
            'name' => 'Alan Romero',
            'email' => 'alan@gmail.com',
            'password' => bcrypt('12345678'),
        ]);
    
    }
}
