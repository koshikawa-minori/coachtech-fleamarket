<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;

class LikeTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    // いいねした商品として登録することができる
    public function test_register_like_item()
    {
    /** @var \App\Models\User $likeUser */
        $likeUser = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($likeUser);

        $item = Item::factory()->create([
            'name' => '腕時計',
            'price' => 15000,
            'brand_name' => 'Rolax',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
            'condition' => 1,
        ]);

        $category = Category::create(['name' => 'メンズ']);
        $item->categories()->attach($category->id);

        $this->assertDatabaseMissing('likes',[
            'user_id' => $likeUser->id,
            'item_id' => $item->id,
        ]);

        $response = $this->post("/item/{$item->id}/like");
        $response->assertRedirect();

        $this->assertDatabaseHas('likes',[
            'user_id' => $likeUser->id,
            'item_id' => $item->id,
        ]);

        $item->refresh()->loadCount('likes');
        $expectedLikesCount = $item->likes_count;

        $likedResponse = $this->get("/item/{$item->id}");
        $likedResponse->assertStatus(200);
        $likedResponse->assertSeeText((string)$expectedLikesCount);
    }

    // 追加済みのアイコンは色が変化する
    public function test_register_like_color_change()
    {
    /** @var \App\Models\User $likeUser */
        $likeUser = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($likeUser);

        $item = Item::factory()->create([
            'name' => '腕時計',
            'price' => 15000,
            'brand_name' => 'Rolax',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
            'condition' => 1,
        ]);

        $category = Category::create(['name' => 'メンズ']);
        $item->categories()->attach($category->id);

        $likeResponse = $this->get("/item/{$item->id}");
        $likeResponse->assertStatus(200);
        $likeResponse->assertDontSee('like-button--active');

        $response = $this->post("/item/{$item->id}/like");
        $response->assertRedirect();

        $likedResponse = $this->get("/item/{$item->id}");
        $likedResponse->assertStatus(200);
        $likedResponse->assertSee('like-button--active');
    }

    //いいねを解除することができる
    public function test_can_remove_like()
    {
    /** @var \App\Models\User $likeUser */
        $likeUser = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($likeUser);

        $item = Item::factory()->create([
            'name' => '腕時計',
            'price' => 15000,
            'brand_name' => 'Rolax',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
            'condition' => 1,
        ]);

        $category = Category::create(['name' => 'メンズ']);
        $item->categories()->attach($category->id);

        $likeUser->likes()->attach($item->id);
        $this->assertDatabaseHas('likes',[
            'user_id' => $likeUser->id,
            'item_id' => $item->id,
        ]);

        $response = $this->delete("/item/{$item->id}/like");
        $response->assertRedirect();

        $this->assertDatabaseMissing('likes',[
            'user_id' => $likeUser->id,
            'item_id' => $item->id,
        ]);

        $item->refresh()->loadCount('likes');
        $expectedLikesCount = $item->likes_count;

        $likeResponse = $this->get("/item/{$item->id}");
        $likeResponse->assertStatus(200);
        $likeResponse->assertSeeText((string)$expectedLikesCount);

    }
}
