<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;

class ItemTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // 全商品を取得できる
    public function test_item_list()
    {
        $itemA = Item::factory()->create([
            'name' => 'メイクセット',
            'price' => 2500,
            'brand_name' => null,
            'description' => '便利なメイクアップセット',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
            'condition' => 1,
            'is_sold' => false,
        ]);

        $itemB = Item::factory()->create([
            'name' => 'コーヒーミル',
            'price' => 4000,
            'brand_name' => 'Starbacks',
            'description' => '手動のコーヒーミル',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
            'condition' => 1,
            'is_sold' => false,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200)->assertViewHas('items');

        $response->assertSeeText($itemA->name);
        $response->assertSeeText($itemB->name);

    }

    // 購入済み商品は「Sold」と表示される
    public function test_item_sold()
    {
        $item = Item::factory()->create([
            'name' => 'メイクセット',
            'price' => 2500,
            'brand_name' => null,
            'description' => '便利なメイクアップセット',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
            'condition' => 2,
            'is_sold' => true,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertSee('Sold');
    }

    // 自分が出品した商品は表示されない
    public function test_my_items_are_not_displayed()
    {
        /** @var \App\Models\User $authenticatedUser */
        $authenticatedUser = User::factory()->create(['email_verified_at' => now()]);

        $category = Category::create(['name' => 'テストカテゴリ']);

        $myItem = Item::factory()->create([
            'name' => 'メイクセット',
            'price' => 2500,
            'brand_name' => null,
            'description' => '便利なメイクアップセット',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
            'seller_user_id' => $authenticatedUser->id,
            'condition' => 2,
            'is_sold' => false,
        ]);
        $myItem->categories()->attach($category->id);

        $response = $this->actingAs($authenticatedUser)->get('/?tab=recommend');

        $response->assertStatus(200)->assertViewHas('items');

        $response->assertDontSeeText($myItem->name);

    }

    // いいねした商品だけが表示される
    public function test_mylist_only_liked()
    {

        /** @var \App\Models\User $authenticatedUser */
        $authenticatedUser = User::factory()->create(['email_verified_at' => now()]);

         /** @var \App\Models\User $otherSeller */
        $otherSellerA = User::factory()->create(['email_verified_at' => now()]);

        $category = Category::create(['name' => 'テストカテゴリ']);

        $otherItemA = Item::factory()->create([
            'name' => 'メイクセット',
            'price' => 2500,
            'brand_name' => null,
            'description' => '便利なメイクアップセット',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
            'seller_user_id' => $otherSellerA->id,
            'condition' => 2,
            'is_sold' => false,
        ]);
        $otherItemA->categories()->attach($category->id);

        $otherItemB = Item::factory()->create([
            'name' => 'コーヒーミル',
            'price' => 4000,
            'brand_name' => 'Starbacks',
            'description' => '手動のコーヒーミル',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
            'seller_user_id' => $otherSellerA->id,
            'condition' => 1,
            'is_sold' => false,
        ]);
        $otherItemB->categories()->attach($category->id);

        $authenticatedUser->likes()->attach($otherItemA->id);


        $response = $this->actingAs($authenticatedUser)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSeeText('メイクセット');
        $response->assertDontSeeText('コーヒーミル');

    }

    // 購入済み商品は「Sold」と表示される
    public function test_mylist_sold()
    {

        /** @var \App\Models\User $authenticatedUser */
        $authenticatedUser = User::factory()->create(['email_verified_at' => now()]);

         /** @var \App\Models\User $otherSeller */
        $otherSellerA = User::factory()->create(['email_verified_at' => now()]);

        $category = Category::create(['name' => 'テストカテゴリ']);

        $soldItem = Item::factory()->create([
            'name' => 'メイクセット',
            'price' => 2500,
            'brand_name' => null,
            'description' => '便利なメイクアップセット',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
            'seller_user_id' => $otherSellerA->id,
            'condition' => 2,
            'is_sold' => true,
        ]);
        $soldItem->categories()->attach($category->id);

        $unsoldItem  = Item::factory()->create([
            'name' => 'コーヒーミル',
            'price' => 4000,
            'brand_name' => 'Starbacks',
            'description' => '手動のコーヒーミル',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
            'seller_user_id' => $otherSellerA->id,
            'condition' => 1,
            'is_sold' => false,
        ]);
        $unsoldItem->categories()->attach($category->id);

        $authenticatedUser->likes()->attach($soldItem->id);
        $authenticatedUser->likes()->attach($unsoldItem->id);

        $response = $this->actingAs($authenticatedUser)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSeeText($soldItem->name);
        $response->assertSeeText($unsoldItem->name);

        $this->assertEquals(1, substr_count($response->getContent(), 'Sold'));
    }

    // 未認証の場合は何も表示されない
    public function test_mylist_not_authenticated()
    {
        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200)->assertViewHas('items', function ($items) {
            return $items->count() === 0;
        });
    }

}
