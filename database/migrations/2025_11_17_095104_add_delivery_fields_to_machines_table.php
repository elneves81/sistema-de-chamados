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
            // Campos de contrato/licitação
            $table->string('contrato_licitacao')->nullable()->after('observacoes');
            $table->string('numero_licitacao')->nullable()->after('contrato_licitacao');
            
            // Campos de troca de máquina
            $table->boolean('is_troca')->default(false)->after('numero_licitacao');
            $table->string('patrimonio_substituido')->nullable()->after('is_troca');
            $table->text('motivo_troca')->nullable()->after('patrimonio_substituido');
            
            // Campos de entrega/recebimento
            $table->unsignedBigInteger('recebedor_id')->nullable()->after('motivo_troca');
            $table->foreign('recebedor_id')->references('id')->on('users')->nullOnDelete();
            $table->dateTime('data_entrega')->nullable()->after('recebedor_id');
            $table->text('assinatura_digital')->nullable()->after('data_entrega'); // Base64 da assinatura
            $table->string('ip_entrega')->nullable()->after('assinatura_digital');
            $table->unsignedBigInteger('entregue_por_id')->nullable()->after('ip_entrega'); // Técnico que fez a entrega
            $table->foreign('entregue_por_id')->references('id')->on('users')->nullOnDelete();
            $table->text('observacoes_entrega')->nullable()->after('entregue_por_id');
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
            $table->dropForeign(['recebedor_id']);
            $table->dropForeign(['entregue_por_id']);
            $table->dropColumn([
                'contrato_licitacao',
                'numero_licitacao',
                'is_troca',
                'patrimonio_substituido',
                'motivo_troca',
                'recebedor_id',
                'data_entrega',
                'assinatura_digital',
                'ip_entrega',
                'entregue_por_id',
                'observacoes_entrega'
            ]);
        });
    }
};
