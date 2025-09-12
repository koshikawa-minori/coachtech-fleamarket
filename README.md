
# coachtechフリマ

## 環境構築
- git clone <リポジトリURL>
- docker-compose up -d --build
- docker-compose exec php bash
- composer install
- cp .env.example .env （環境変数を変更）
- php artisan key:generate
- php artisan migrate
- php artisan db:seed

## 使用技術（実行環境）
- PHP 8.x
- Laravel 12.x
- MySQL 8.0
- nginx 1.21

## ER図
![ER図](docs/er.png)

## URL
- 開発環境: http://localhost/
- phpMyAdmin: http://localhost:8080/
