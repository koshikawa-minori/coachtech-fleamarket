CREATE DATABASE IF NOT EXISTS coachtech_fleamarket_test
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

GRANT ALL PRIVILEGES ON coachtech_fleamarket_test.* TO 'root'@'%';
GRANT ALL PRIVILEGES ON coachtech_fleamarket_test.* TO 'laravel'@'%';
FLUSH PRIVILEGES;
