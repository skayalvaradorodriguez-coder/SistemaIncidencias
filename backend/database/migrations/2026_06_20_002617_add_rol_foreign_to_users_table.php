<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('rol_id')->nullable()->after('id');
            $table->string('apellido', 100)->after('name');
            $table->boolean('activo')->default(true)->after('password');

            $table->foreign('rol_id')
                  ->references('id')
                  ->on('roles')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['rol_id']);
            $table->dropColumn(['rol_id', 'apellido', 'activo']);
        });
    }
};