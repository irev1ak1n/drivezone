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
        Schema::create('reviews', function (Blueprint $table) {
            $table->string('id', 15)->primary();

            $table->uuid('client_id');
            $table->uuid('employee_id')->nullable();

            $table->uuid('subject_id');
            $table->enum('subject_type', ['Сервис', 'Товар']);

            $table->decimal('rating', 2, 1);
            $table->string('comment', 500)->nullable();

            $table->enum('status', ['Доступен к чтению', 'Удалён'])->default('Доступен к чтению');

            $table->timestamps();

            $table->foreign('client_id')
                ->references('id')->on('users')->onDelete('cascade');
            $table->foreign('employee_id')
                ->references('id')->on('users')->onDelete('set null');

            // индекс для быстрого поиска по паре (subject_id + subject_type)
            $table->index(['subject_id', 'subject_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
