<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

// use function Symfony\Component\Clock\now;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => '一般ユーザー',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'seller@example.com'],
            [
                'name' => 'デモ出品者',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}
