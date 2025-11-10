<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id(); // автоинкремент, PRIMARY KEY

            // FK на users
            $table->uuid('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->string('position');                   // должность
            $table->string('work_shift');                 // смена (например: утренняя, ночная)
            $table->decimal('salary', 10, 2);             // зарплата
            $table->dateTime('hire_date');                // дата найма
            $table->enum('is_active', ['Свободен', 'Занят'])->default('Свободен'); // доступен ли сотрудник
            $table->string('skills')->default('Не указано');        // навыки
            $table->decimal('rating', 3, 2)->default(0);  // рейтинг (например 4.75)
            $table->integer('experience_years')->default(0); // опыт (лет)

            // FK на руководителя (supervisor)
            $table->uuid('supervisor_id')->nullable();
            $table->foreign('supervisor_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            $table->timestamp('note_added_at')->nullable(); // когда была добавлена заметка

            $table->string('notes', 255)->default('Нет заметок');        // заметки
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
