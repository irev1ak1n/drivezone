<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('image_url')->nullable();

            // связь с брендом
            $table->string('brand_id');
            $table->foreign('brand_id')
                ->references('id')->on('brands')->onDelete('cascade');

            // имя + описание
            $table->string('name');
            $table->text('product_description')->nullable();

            // цена
            $table->decimal('price', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
