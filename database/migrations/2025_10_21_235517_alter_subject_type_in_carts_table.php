<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // изменяем тип поля subject_type с enum → string(100)
            $table->string('subject_type', 100)->change();
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // при откате возвращаем enum, если нужно
            $table->enum('subject_type', ['product', 'service'])->change();
        });
    }
};
