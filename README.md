
# coachtechフリマ

## 環境構築手順

### 1. Docker ビルド
```bash
git clone https://github.com/koshikawa-minori/coachtech-fleamarket.git
docker-compose up -d --build
```

### 2. Laravel 環境構築
```bash
docker-compose exec php bash
composer install
cp .env.example .env  #環境変数を変更
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link  #画像表示のために必要
```

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
- 会員登録: http://localhost/register
- ログイン: http://localhost/login

## テストユーザー情報

| ユーザー種別 | メールアドレス | パスワード |
|---------------|----------------|-------------|
| 一般ユーザー | test@example.com | password |

## メール認証機能（応用）

本アプリでは**Mailtrap**を利用して
新規会員登録時にメール認証を行います

1 .env に Mailtrap の MAIL_USERNAME / MAIL_PASSWORD を設定
2 `/register`(会員登録画面) で新規登録を行う
3 登録直後は `/register/verify`（メール認証誘導画面）へ遷移
4 「認証はこちらから」ボタン押下で認証メールを送信し、`/email/verify`（メール認証画面）へ遷移
5 認証メール内のリンクをクリックすると、新しいタブで `/email/verify/{id}/{hash}` にアクセスし、認証が完了してプロフィール設定画面が開く
- 認証が未完了のままログインした場合も認証誘導画面へ遷移
- 認証メールの再送機能あり（1分間に3回まで）
