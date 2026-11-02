<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
             'name' => 'Administrator',
             'username' => 'Administrator',
             'email' => 'admin@mail.com',
             'password' => \Hash::make('12345678'),
             'level' => 'Administrator',
             'created_at' => now(),
         ]);
        
    }
}
