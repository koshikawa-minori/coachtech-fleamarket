<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        $response = $this->get('/');

        $response->assertStatus(200);
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
    public function test_hide_items()
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
    //$items = $response->viewData('items');
    //$response->assertSeeText($otherItem->name);
    //$this->assertCount(0, $collection);

}
