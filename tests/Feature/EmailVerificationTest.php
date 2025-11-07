<?php

namespace Tests\Feature;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
use App\Models\User;


class EmailVerificationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // 会員登録後、認証メールが送信される+メール認証誘導画面へ遷移
    public function test_verification_email_is_sent_redirects_to_verification_page_after_register()
    {
        Notification::fake();

        $formData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $formData);
        $response->assertRedirect(route('register.verify'));

        $registeredUser = User::where('email', 'test@example.com')->first();
        Notification::assertSentTo($registeredUser, VerifyEmail::class);

    }

    // メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する
    public function test_click_navigates_to_verification()
    {
        Notification::fake();

        $formData = [
                'name' => 'テストユーザー',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $formData);
        $response->assertRedirect(route('register.verify'));

        $registeredUser = User::where('email', 'test@example.com')->first();
        Notification::assertSentTo($registeredUser, VerifyEmail::class);

        $response = $this->actingAs($registeredUser)->get(route('verification.notice'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.verify');

    }

    // メール認証を完了すると、プロフィール設定画面に遷移する
    public function test_redirects_to_profile_after_email_verification()
    {
        Notification::fake();

        $formData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $this->post('/register', $formData);

        $registeredUser = User::where('email', 'test@example.com')->first();

        $verifyUrl = URL::temporarySignedRoute('verification.verify',now()->addMinutes(60),
            ['id' => $registeredUser->id,
            'hash' => sha1($registeredUser->email)]);

        $response = $this->actingAs($registeredUser)->get($verifyUrl);

        $response->assertRedirect(route('profile.edit'));

        $this->assertTrue($registeredUser->fresh()->hasVerifiedEmail());

    }
}
