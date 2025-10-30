<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;

class purchaseTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // 「購入する」ボタンを押下すると購入が完了する
    public function test_can_post_comment()
    {
        /** @var \App\Models\User $commentUser */
        $commentUser = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($commentUser);

        $item = Item::factory()->create([
            'name' => 'メイクセット',
            'price' => 2500,
            'brand_name' => null,
            'description' => '便利なメイクアップセット',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
            'condition' => 1,
            'is_sold' => false,
        ]);

        $category = Category::create(['name' => 'コスメ']);
        $item->categories()->attach($category->id);

        $commentData = ['comment' => '気に入りました！'];

        $this->assertDatabaseMissing('comments',[
            'user_id' => $commentUser->id,
            'item_id' => $item->id,
            'comment' => '気に入りました！',
        ]);

        $response = $this->post("/item/{$item->id}/comments", $commentData);
        $response->assertRedirect();

        $this->assertDatabaseHas('comments',[
            'user_id' => $commentUser->id,
            'item_id' => $item->id,
            'comment' => '気に入りました！',
        ]);

        $this->assertDatabaseCount('comments', 1);

        $commentedResponse = $this->get("/item/{$item->id}");
        $commentedResponse->assertStatus(200);
        $commentedResponse->assertSeeText('気に入りました！');
    }

    // 購入した商品は商品一覧画面にて「sold」と表示される
    // 購入済み商品は「Sold」と表示される
    public function test_sold()
    {

        /** @var \App\Models\User $authenticatedUser */
        $authenticatedUser = User::factory()->create(['email_verified_at' => now()]);

         /** @var \App\Models\User $otherSeller */
        $otherSeller = User::factory()->create(['email_verified_at' => now()]);

        $category = Category::create(['name' => 'レディース']);

        $soldItem = Item::factory()->create([
            'name' => 'メイクセット',
            'price' => 2500,
            'brand_name' => null,
            'description' => '便利なメイクアップセット',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
            'seller_user_id' => $otherSeller->id,
            'condition' => 2,
            'is_sold' => true,
        ]);
        $soldItem->categories()->attach($category->id);

        $unsoldItem  = Item::factory()->create([
            'name' => 'ショルダーバッグ',
            'price' => 3500,
            'brand_name' => null,
            'description' => 'おしゃれなショルダーバッグ',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
            'seller_user_id' => $otherSeller->id,
            'condition' => 3,
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

    // 「プロフィール/購入した商品一覧」に追加されている
    // 購入済み商品は「Sold」と表示される
    public function test_mylist_sold()
    {

        /** @var \App\Models\User $authenticatedUser */
        $authenticatedUser = User::factory()->create(['email_verified_at' => now()]);

         /** @var \App\Models\User $otherSeller */
        $otherSeller = User::factory()->create(['email_verified_at' => now()]);

        $category = Category::create(['name' => 'レディース']);

        $soldItem = Item::factory()->create([
            'name' => 'メイクセット',
            'price' => 2500,
            'brand_name' => null,
            'description' => '便利なメイクアップセット',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
            'seller_user_id' => $otherSeller->id,
            'condition' => 2,
            'is_sold' => true,
        ]);
        $soldItem->categories()->attach($category->id);

        $unsoldItem  = Item::factory()->create([
            'name' => 'ショルダーバッグ',
            'price' => 3500,
            'brand_name' => null,
            'description' => 'おしゃれなショルダーバッグ',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
            'seller_user_id' => $otherSeller->id,
            'condition' => 3,
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
}
