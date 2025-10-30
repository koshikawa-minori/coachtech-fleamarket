<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;

class CommentTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // ログイン済みのユーザーはコメントを送信できる
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

    // ログイン前のユーザーはコメントを送信できない
    public function test_guest_cannot_post_comment()
    {
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

        $commentData = [
            'comment' => '気に入りました！'
        ];

        $this->assertDatabaseCount('comments', 0);

        $response = $this->post("/item/{$item->id}/comments", $commentData);

        $response->assertRedirect('/login');
        $this->assertGuest();

        $this->assertDatabaseCount('comments', 0);
        $this->assertDatabaseMissing('comments', ['comment' => '気に入りました！']);

        $commentedResponse = $this->get("/item/{$item->id}");
        $commentedResponse->assertStatus(200);
        $commentedResponse->assertDontSeeText('気に入りました！');
    }

    // コメントが入力されていない場合、バリデーションメッセージが表示される
    public function test_comment_is_required()
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

        $formData = [
            'comment' => '',
        ];

        $response = $this->from("/item/{$item->id}")->post("/item/{$item->id}/comments", $formData);

        $response->assertRedirect("/item/{$item->id}");
        $response->assertSessionHasErrors(['comment']);

        $this->followRedirects($response)->assertSee('コメントを入力してください');
    }

    // コメントが255字以上の場合、バリデーションメッセージが表示される
    public function test_comment_max255()
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

        $formData = [
            'comment' => str_repeat('あ', 256),
        ];

        $response = $this->from("/item/{$item->id}")->post("/item/{$item->id}/comments", $formData);

        $response->assertRedirect("/item/{$item->id}");
        $response->assertSessionHasErrors(['comment']);

        $this->followRedirects($response)->assertSee('コメントは255文字以内で入力してください');
    }

}
