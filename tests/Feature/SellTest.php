<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Item;
use App\Models\Category;

class SellTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // 商品出品画面にて必要な情報が保存できる
    // 必要な情報が取得できる
    public function test_profile_page_shows_user_info()
    {
    /** @var \App\Models\User $authenticatedUser */
        $authenticatedUser = User::factory()->create([
            'name' => 'テストユーザー',
            'email_verified_at' => now()]);
        $this->actingAs($authenticatedUser);

        $authenticatedUser->profile()->create([
            'user_id' => $authenticatedUser->id,
            'image_path' => 'https://example.com/profile.png',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区千駄ヶ谷1-2-3',
        ]);

        $otherUser = User::factory()->create(['email_verified_at' => now()]);

        $category = Category::create(['name' => 'ファッション']);

        $sellItem = Item::factory()->create([
            'name' => 'メイクセット',
            'price' => 2500,
            'brand_name' => null,
            'description' => '便利なメイクアップセット',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
            'seller_user_id' => $authenticatedUser->id,
            'condition' => 2,
            'is_sold' => false,
        ]);
        $sellItem->categories()->attach($category->id);

        $purchasedItem = Item::factory()->create([
            'name' => '腕時計',
            'price' => 15000,
            'brand_name' => 'Rolax',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
            'seller_user_id' => $otherUser->id,
            'condition' => 1,
            'is_sold' => true,
        ]);
        $purchasedItem->categories()->attach($category->id);

        Order::query()->create([
            'buyer_user_id' => $authenticatedUser->id,
            'item_id' => $purchasedItem->id,
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区千駄ヶ谷1-2-3',
            'payment_method' => 1,
        ]);

        $response = $this->get('/mypage');
        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('https://example.com/profile.png');

        $response = $this->get('/mypage?page=sell');
        $response->assertStatus(200);
        $response->assertSee('メイクセット');
        $response->assertDontSee('腕時計');

        $response = $this->get('/mypage?page=buy');
        $response->assertStatus(200);
        $response->assertSee('腕時計');
        $response->assertDontSee('メイクセット');

    }

    // 変更項目が初期値として過去設定されている
    public function test_profile_initial_value_is_set()
    {
    /** @var \App\Models\User $authenticatedUser */
        $authenticatedUser = User::factory()->create([
            'name' => 'テストユーザー',
            'email_verified_at' => now()]);
        $this->actingAs($authenticatedUser);

        $authenticatedUser->profile()->create([
            'user_id' => $authenticatedUser->id,
            'image_path' => 'https://example.com/profile.png',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区千駄ヶ谷1-2-3',
        ]);

        $response = $this->get('/mypage/profile');
        $response->assertStatus(200);
        $response->assertSee('value="テストユーザー"', false);
        $response->assertSee('https://example.com/profile.png');
        $response->assertSee('value="123-4567"', false);
        $response->assertSee('value="東京都渋谷区千駄ヶ谷1-2-3"', false);

    }
}
