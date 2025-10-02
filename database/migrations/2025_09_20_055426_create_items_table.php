<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            //出品者
            $table->foreignId('seller_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            //商品情報
            $table->string('name', 255);
            $table->string('brand_name', 255)->nullable();
            $table->unsignedInteger('price');
            $table->string('description', 255);
            $table->unsignedTinyInteger('condition');
            $table->string('image_path', 255);

            //商品の販売状態
            $table->boolean('is_sold')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
