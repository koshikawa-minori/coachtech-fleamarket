
# coachtechフリマ

## 環境構築

### Docker ビルド
- git clone https://github.com/koshikawa-minori/coachtech-fleamarket/tree/main
- docker-compose up -d --build

### Laravel 環境構築
- docker-compose exec php bash
- composer install
- cp .env.example .env （環境変数を変更）
- php artisan key:generate
- php artisan migrate
- php artisan db:seed
- php artisan storage:link （画像表示のために必要）

## 使用技術（実行環境）
- PHP 8.x
- Laravel 12.x
- MySQL 8.0
- nginx 1.21

## ER図
![ER図](docs/coachtech-fleamarket-ER.png)

## 開発環境URL
- 開発環境: http://localhost/
- phpMyAdmin: http://localhost:8080/
- ユーザー登録: http://localhost/register
- ログイン: http://localhost/login

## テストユーザー情報

| ユーザー種別 | メールアドレス | パスワード |
|---------------|----------------|-------------|
| 一般ユーザー | test@example.com | password |

## メール認証機能（応用）
- 新規会員登録時にメール認証を実施
- 登録直後は `/register/verify`（メール認証誘導画面）へ遷移
- 「認証はこちらから」ボタン押下で認証メールを送信し、`/email/verify`（メール認証画面）へ遷移
- 認証メール内のリンクをクリックすると、新しいタブで `/email/verify/{id}/{hash}` にアクセスし、認証が完了してプロフィール設定画面が開く
- 認証が未完了のままログインした場合も認証誘導画面へ遷移
- 認証メールの再送機能あり（1分間に3回まで）
