<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;

class AddressTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    // 送付先住所変更画面にて登録した住所が商品購入画面に反映されている
    public function test_registered_address_is_reflected()
    {
        /** @var \App\Models\User $buyerUser */
        $buyerUser = User::factory()
        ->create(['email_verified_at' => now()]);
        $this->actingAs($buyerUser);

        $buyerUser->profile()->create([
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区千駄ヶ谷1-2-3',
        ]);

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

        $response = $this->post("/purchase/address/{$item->id}", [
            'postal_code' => '987-6543',
            'address' => '東京都渋谷区千駄ヶ谷4-5-6',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('profiles',[
            'user_id' => $buyerUser->id,
            'postal_code' => '987-6543',
            'address' => '東京都渋谷区千駄ヶ谷4-5-6',
        ]);

        $get = $this->get("/purchase/{$item->id}");
        $get->assertStatus(200)->assertSeeText('〒987-6543');
        $get->assertSeeText('東京都渋谷区千駄ヶ谷4-5-6');

    }

    // 購入した商品に送付先住所が紐づいて登録される
    public function test_shipping_address_is_linked()
    {
        /** @var \App\Models\User $buyerUser */
        $buyerUser = User::factory()
        ->create(['email_verified_at' => now()]);
        $this->actingAs($buyerUser);

        $buyerUser->profile()->create([
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区千駄ヶ谷1-2-3',
        ]);

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

        $response = $this->post("/purchase/address/{$item->id}", [
            'postal_code' => '987-6543',
            'address' => '東京都渋谷区千駄ヶ谷4-5-6',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('profiles',[
            'user_id' => $buyerUser->id,
            'postal_code' => '987-6543',
        ]);

        $get = $this->get("/purchase/{$item->id}");
        $get->assertStatus(200)->assertSeeText('〒987-6543');
        $get->assertSeeText('東京都渋谷区千駄ヶ谷4-5-6');

        $this->assertDatabaseMissing('orders',[
            'buyer_user_id' => $buyerUser->id,
            'item_id' => $item->id,
            'payment_method' => 1,
        ]);

        $response = $this->post("/purchase/{$item->id}", ['payment_method' => 1]);
        $response->assertStatus(302);

        $response->assertHeader('Location');

        $this->assertMatchesRegularExpression(
            '#^https://checkout\.stripe\.com/#',
            $response->headers->get('Location')
        );

        $this->assertDatabaseHas('orders',[
            'buyer_user_id' => $buyerUser->id,
            'item_id' => $item->id,
            'payment_method' => 1,
            'postal_code' => '987-6543',
            'address' => '東京都渋谷区千駄ヶ谷4-5-6',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'is_sold' => true,
        ]);

        $this->assertDatabaseCount('orders', 1);

    }

}
