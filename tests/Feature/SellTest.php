<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;

class SellTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // 商品出品画面にて必要な情報が保存できる
    public function test_sell_stores_item_and_redirects()
    {
    /** @var \App\Models\User $authenticatedUser */
        $authenticatedUser = User::factory()->create([
            'name' => 'テストユーザー',
            'email_verified_at' => now()]);
        $this->actingAs($authenticatedUser);

        $category = Category::create(['name' => 'メンズ']);

        Storage::fake('public');

        $response=$this->post("/sell",[
            'name' => '腕時計',
            'price' => 15000,
            'brand' => 'Rolax',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'condition' => 1,
            'image' => UploadedFile::fake()->create('test.jpg', 10, 'image/jpeg'),
            'category_ids' => [$category->id],
        ]);

        $response->assertRedirect(route('items.index'))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('items',[
            'name' => '腕時計',
            'price' => 15000,
            'brand_name' => 'Rolax',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'condition' => 1,
            'seller_user_id' => $authenticatedUser->id,
            'is_sold' => 0,
        ]);

        $this->assertDatabaseHas('category_items',[
            'category_id' => $category->id,
        ]);


    }

}
