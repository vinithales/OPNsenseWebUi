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
        Schema::table('users', function (Blueprint $table) {
            $table->string('ra')->nullable()->unique()->after('email')->comment('Registro Acadêmico');
            $table->enum('user_type', ['aluno', 'professor', 'admin'])->default('aluno')->after('ra')->comment('Tipo de usuário');
            $table->enum('status', ['ativo', 'inativo', 'bloqueado'])->default('ativo')->after('user_type')->comment('Status do usuário');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ra', 'user_type', 'status']);
        });
    }
};
