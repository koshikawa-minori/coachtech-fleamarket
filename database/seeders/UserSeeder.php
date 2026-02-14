<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $buyer = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => '一般ユーザー',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $seller = User::updateOrCreate(
            ['email' => 'seller@example.com'],
            [
                'name' => 'デモ出品者',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $seller2 = User::updateOrCreate(
            ['email' => 'seller2@example.com'],
            [
                'name' => 'デモ出品者2',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        Profile::updateOrCreate(
            ['user_id' => $buyer->id],
            [
                'image_path' => null,
            ],
        );

        Profile::updateOrCreate(
            ['user_id' => $seller->id],
            [
                'image_path' => null,
            ],
        );

        Profile::updateOrCreate(
            ['user_id' => $seller2->id],
            [
                'image_path' => null,
            ],
        );

    }
}
