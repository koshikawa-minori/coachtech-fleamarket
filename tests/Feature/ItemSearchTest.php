<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;

class ItemSearchTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    // 「商品名」で部分一致検索ができる
    public function test_it_can_search_items()
    {
        $category = Category::create(['name' => 'キッチン']);

        $item  = Item::factory()->create([
            'name' => 'コーヒーミル',
            'price' => 4000,
            'brand_name' => 'Starbacks',
            'description' => '手動のコーヒーミル',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
            'condition' => 1,
            'is_sold' => false,
        ]);
        $item->categories()->attach($category->id);

        $response = $this->get('/?keyword=ミル');

        $response->assertStatus(200);
        $response->assertSee('コーヒーミル');

    }

    // 検索状態がマイリストでも保持されている
    public function test_search_keyword_on_mylist()
    {
        /** @var \App\Models\User $authenticatedUser */
        $authenticatedUser = User::factory()->create(['email_verified_at' => now()]);

        $category = Category::create(['name' => 'キッチン']);

        $item  = Item::factory()->create([
            'name' => 'コーヒーミル',
            'price' => 4000,
            'brand_name' => 'Starbacks',
            'description' => '手動のコーヒーミル',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
            'condition' => 1,
            'is_sold' => false,
        ]);
        $item->categories()->attach($category->id);

        $response = $this->actingAs($authenticatedUser)->get('/?tab=mylist&keyword=ミル');

        $response->assertStatus(200);

        $response->assertSee('value="ミル"', false);
    }
}
