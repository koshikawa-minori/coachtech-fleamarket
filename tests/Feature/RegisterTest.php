<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    // 名前が未入力の場合、エラーメッセージが表示される
    public function test_name_is_required()
    {
        $formData = [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->from('/register')->post('/register', $formData);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['name']);

        $this->followRedirects($response)->assertSee('お名前を入力してください');
    }

    // メールアドレスが未入力の場合、エラーメッセージが表示される
    public function test_email_is_required()
    {
        $formData = [
            'name' => 'test',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->from('/register')->post('/register', $formData);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['email']);

        $this->followRedirects($response)->assertSee('メールアドレスを入力してください');
    }

    // パスワードが未入力の場合、エラーメッセージが表示される
    public function test_password_is_required()
    {
        $formData = [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => 'password',
        ];

        $response = $this->from('/register')->post('/register', $formData);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password']);

        $this->followRedirects($response)->assertSee('パスワードを入力してください');
    }

    // パスワードが7文字以下の場合、エラーメッセージが表示される
    public function test_password_min8()
    {
        $formData = [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'passwor',
            'password_confirmation' => 'password',
        ];

        $response = $this->from('/register')->post('/register', $formData);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password']);

        $this->followRedirects($response)->assertSee('パスワードは8文字以上で入力してください');
    }

    // パスワードが確認用パスワードと一致しない場合、エラーメッセージが表示される
    public function test_password_confirmation_mismatch()
    {
        $formData = [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'passwordd',
        ];

        $response = $this->from('/register')->post('/register', $formData);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password_confirmation']);

        $this->followRedirects($response)->assertSee('パスワードと一致しません');
    }

    // メール認証導入済みのため、登録後はメール認証画面へ遷移する
    // プロフィール画面への遷移テストは記述していない
}
