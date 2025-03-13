<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Сначала проверяем существование колонки
            if (Schema::hasColumn('users', 'role')) {
                // Если колонка существует, изменяем её
                $table->string('role')->default('student')->change();
            } else {
                // Если колонки нет, создаём новую
                $table->string('role')->default('student');
            }
        });

        // Обновляем существующих пользователей
        DB::table('users')->whereNull('role')->update(['role' => 'student']);
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // В down() не удаляем колонку, так как она может быть нужна другим миграциям
            $table->string('role')->nullable()->change();
        });
    }
};
