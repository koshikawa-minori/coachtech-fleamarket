<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;

class ItemShowTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // 必要な情報が表示される
    public function test_item_required_information_display()
    {
        /** @var \App\Models\User $commentUser */
        $commentUser = User::factory()->create(['email_verified_at' => now()]);

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
        $commentUser->likes()->attach($item->id);

        Comment::create([
            'item_id' => $item->id,
            'user_id' => $commentUser->id,
            'comment' => '気に入りました！',
        ]);

        Comment::create([
            'item_id' => $item->id,
            'user_id' => $commentUser->id,
            'comment' => 'とても良いと思います。',
        ]);

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee('https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg');
        $response->assertSeeText('腕時計');
        $response->assertSeeText('Rolax');
        $response->assertSeeText('15,000');
        $response->assertSeeText("{$item->likes->count()}");
        $response->assertSeeText("{$item->comments->count()}");
        $response->assertSeeText('スタイリッシュなデザインのメンズ腕時計');
        $response->assertSeeText('メンズ');
        $response->assertSeeText('良好');
        $response->assertSeeText($commentUser->name);
        $response->assertSeeText('気に入りました！');
    }

    // 複数選択されたカテゴリが表示されている
    public function test_multiple_categories_are_displayed()
    {
        $item = Item::factory()->create([
            'name' => '腕時計',
            'price' => 15000,
            'brand_name' => 'Rolax',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
            'condition' => 1,
        ]);

        $categoryA = Category::create([
            'name' => 'メンズ',
        ]);

        $categoryB = Category::create([
            'name' => 'ファッション',
        ]);

        $item->categories()->attach([$categoryA->id, $categoryB->id]);

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);
        $response->assertSeeText('メンズ');
        $response->assertSeeText('ファッション');
    }
}
