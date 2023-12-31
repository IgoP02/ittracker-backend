<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::create([
            "username" => "admin",
            "email" => "admin@invecem.com",
            "password" => Hash::make("itssb2000"),
        ]);
    }
}
