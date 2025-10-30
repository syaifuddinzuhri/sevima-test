<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 1; $i <= 50; $i++) {
            $name = $faker->name;
            $username = Str::slug($name, '_');
            $email = $username . "@example.com";

            User::create([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make('password1234'),
            ]);
        }
    }
}
