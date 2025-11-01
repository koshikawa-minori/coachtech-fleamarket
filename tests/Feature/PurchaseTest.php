<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;
use App\Models\Category;

class PurchaseTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // 「購入する」ボタンを押下すると購入が完了する
    public function test_press_button_to_buy()
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
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'is_sold' => true,
        ]);

        $this->assertDatabaseCount('orders', 1);
    }

    // 購入した商品は商品一覧画面にて「sold」と表示される
    public function test_item_list_sold()
    {
         /** @var \App\Models\User $buyerUser */
        $buyerUser = User::factory()
        ->create(['email_verified_at' => now()]);
        $this->actingAs($buyerUser);

        $buyerUser->profile()->create([
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区千駄ヶ谷1-2-3',
        ]);

        $sellerUser = User::factory()->create(['email_verified_at' => now()]);

        $item = Item::factory()->for($sellerUser, 'seller')->create([
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
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'is_sold' => true,
        ]);

        $this->assertDatabaseCount('orders', 1);

        $response = $this->get('/?tab=recommend');

        $response->assertStatus(200);
        $response->assertSeeText($item->name);

        $this->assertEquals(1, substr_count($response->getContent(), 'Sold'));

    }

    // 「プロフィール/購入した商品一覧」に追加されている
    public function test_purchase_list_shows_sold()
    {
         /** @var \App\Models\User $buyerUser */
        $buyerUser = User::factory()
        ->create(['email_verified_at' => now()]);
        $this->actingAs($buyerUser);

        $buyerUser->profile()->create([
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区千駄ヶ谷1-2-3',
        ]);

        $sellerUser = User::factory()->create(['email_verified_at' => now()]);

        $item = Item::factory()->for($sellerUser, 'seller')->create([
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
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'is_sold' => true,
        ]);

        $this->assertDatabaseCount('orders', 1);

        $response = $this->get('/mypage?page=buy');

        $response->assertStatus(200);
        $response->assertSeeText($item->name);

        $this->assertEquals(1, substr_count($response->getContent(), 'Sold'));

    }

    // 小計画面で変更が反映される
    public function test_subtotal_reflects_payment()
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

        $response = $this->get("/purchase/{$item->id}");
        $response->assertStatus(200);

        $response->assertSee('コンビニ支払い');
        $response->assertSee('カード支払い');

        $response = $this->get("/purchase/{$item->id}?payment_method=" . Order::PAYMENT_CONVENIENCE_STORE_PAYMENT);
        $response->assertSee('コンビニ支払い');
    }

}
