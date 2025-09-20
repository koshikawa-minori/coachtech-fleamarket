<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;

class ItemDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = User::query()->value('id');
        if (!$userId) {
            $userId = User::factory()->create()->id;
        }

    $rows = [
        ['腕時計', 15000, 'Rolax', 'スタイリッシュなデザインのメンズ腕時計', 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg', 1],
        ['HDD', 5000, '西芝', '高速で信頼性の高いハードディスク', 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg', 2],
        ['玉ねぎ3束', 300, 'なし', '新鮮な玉ねぎ3束のセット', 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg', 3],
        ['革靴', 4000, null, 'クラシックなデザインの革靴', 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg', 4],
        ['ノートPC', 45000, null, '高性能なノートパソコン', 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg', 1],
        ['マイク', 8000, 'なし', '高音質のレコーディング用マイク', 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg', 2],
        ['ショルダーバッグ', 3500, null, 'おしゃれなショルダーバッグ', 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg', 3],
        ['タンブラー', 500, 'なし', '使いやすいタンブラー', 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg', 4],
        ['コーヒーミル', 4000, 'Starbacks', '手動のコーヒーミル', 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg', 1],
        ['メイクセット', 2500, null, '便利なメイクアップセット', 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg', 2],
    ];

    foreach ($rows as [$name, $price, $brand, $description, $image, $condition]) {
        Item::create([
            'user_id' => $userId,
            'name' => $name,
            'brand_name' => $brand === 'なし' ? null : $brand,
            'price' => $price,
            'description' => $description,
            'condition' => $condition,
            'image_path' => $image,
            'is_sold' => false,
        ]);
        }
    }
}
