<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // LikeSeeder: 機能確認済みだが、今回のダミーデータ作成対象外のため未呼び出し
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ItemDemoSeeder::class,
            TransactionSeeder::class,
            //LikeSeeder::class,
        ]);
    }
}
