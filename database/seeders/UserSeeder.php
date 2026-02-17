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
        $generalUser = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => '一般ユーザー',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $buyer = User::updateOrCreate(
            ['email' => 'buyer@example.com'],
            [
                'name' => '購入者',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $seller = User::updateOrCreate(
            ['email' => 'seller@example.com'],
            [
                'name' => '出品者',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $seller2 = User::updateOrCreate(
            ['email' => 'seller2@example.com'],
            [
                'name' => '出品者2',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        Profile::updateOrCreate(
            ['user_id' => $generalUser->id],
            [
                'image_path' => null,
            ],
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
