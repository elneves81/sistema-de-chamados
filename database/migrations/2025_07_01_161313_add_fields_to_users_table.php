<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['customer', 'technician', 'admin'])->default('customer'); // Perfil do usuário
            $table->string('phone')->nullable(); // Telefone
            $table->string('department')->nullable(); // Departamento
            $table->boolean('is_active')->default(true); // Usuário ativo
            $table->timestamp('last_login_at')->nullable(); // Último login
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'department', 'is_active', 'last_login_at']);
        });
    }
};
