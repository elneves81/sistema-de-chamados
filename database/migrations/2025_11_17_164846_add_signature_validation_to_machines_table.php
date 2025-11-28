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
        Schema::table('machines', function (Blueprint $table) {
            $table->enum('assinatura_status', ['nao_requerida', 'pendente', 'validada'])
                  ->default('nao_requerida')
                  ->after('nome_legivel_assinatura')
                  ->comment('Status da assinatura: nao_requerida, pendente, validada');
            
            $table->timestamp('assinatura_validada_em')->nullable()->after('assinatura_status');
            $table->foreignId('assinatura_validada_por')->nullable()->constrained('users')->nullOnDelete()->after('assinatura_validada_em');
            $table->string('assinatura_usuario_validador')->nullable()->after('assinatura_validada_por')->comment('Login do usuário que validou');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->dropForeign(['assinatura_validada_por']);
            $table->dropColumn(['assinatura_status', 'assinatura_validada_em', 'assinatura_validada_por', 'assinatura_usuario_validador']);
        });
    }
};
