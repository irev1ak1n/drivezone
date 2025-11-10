<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // связь с пользователем
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // полиморфная связь (теперь string вместо enum)
            $table->uuid('subject_id');
            $table->string('subject_type', 100); // достаточно места для полного имени класса

            $table->integer('quantity')->default(1);
            $table->string('note')->nullable();

            $table->timestamps();

            // индекс для оптимизации поиска
            $table->index(['subject_id', 'subject_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
