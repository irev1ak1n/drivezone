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
        Schema::create('users', function (Blueprint $table) {
            $table->string('id', 15)->primary();     // UUID как PK
            $table->string('first_name');                  // имя
            $table->string('last_name');                   // фамилия
            $table->string('photo_url')->nullable();       // фото (может быть пустым)
            $table->enum('gender', ['male', 'female']);    // пол
            $table->string('email')->unique();             // уникальная почта
            $table->timestamp('email_verified_at')->nullable(); // время подтверждения email
            $table->string('phone_number')->unique()->nullable();    // телефон
            $table->string('password');               // пароль (Hash::make)
            $table->enum('role', ['admin', 'customer', 'employee', 'supervisor']);   // роль
            $table->timestamps();                          // created_at, updated_at
            $table->rememberToken();                        // токен "запомнить меня"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
