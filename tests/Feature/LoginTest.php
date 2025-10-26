<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Profile;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // メールアドレスが未入力の場合、エラーメッセージが表示される
    public function test_email_is_required()
    {
        $formData = [
            'email' => '',
            'password' => 'password',
        ];

        $response = $this->from('/login')->post('/login', $formData);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email']);

        $this->followRedirects($response)->assertSee('メールアドレスを入力してください');
    }

    // パスワードが未入力の場合、エラーメッセージが表示される
    public function test_password_is_required()
    {
        $formData = [
            'email' => 'test@example.com',
            'password' => '',
        ];

        $response = $this->from('/login')->post('/login', $formData);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['password']);

        $this->followRedirects($response)->assertSee('パスワードを入力してください');
    }

    // 入力情報が間違えている場合、エラーメッセージが表示される
    public function test_input_information_error()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $formData = [
            'email' => 'test@example.com',
            'password' => 'passwordd',
        ];

        $response = $this->from('/login')->post('/login', $formData);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email']);

        $this->followRedirects($response)->assertSee('ログイン情報が登録されていません');
    }

    // 正しい情報が入力された場合、ログイン処理が実行される
    public function test_all_matched()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        Profile::create([
            'user_id' => $user->id,
            'image_path' => null,
            'name' => 'test',
            'postal_code' => null,
            'address' => null,
            'building' => null,
        ]);

        $formData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->withSession(['url.intended' => '/?tab=mylist'])->from('/login')->post('/login', $formData);

        $response->assertRedirect('/?tab=mylist');

        $this->assertAuthenticatedAs($user);

    }

    // ログアウトができる
    public function test_logout()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        Profile::create([
            'user_id' => $user->id,
            'image_path' => null,
            'name' => 'test',
            'postal_code' => null,
            'address' => null,
            'building' => null,
        ]);

        $this->actingAs($user);
        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();

    }

}
