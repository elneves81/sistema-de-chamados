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
        Schema::table('tickets', function (Blueprint $table) {
            // Adiciona técnico de suporte/auxiliar ao chamado
            $table->foreignId('support_technician_id')
                  ->nullable()
                  ->after('assigned_to')
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Técnico auxiliar/suporte do chamado');
            
            // Índice para performance em consultas
            $table->index('support_technician_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['support_technician_id']);
            $table->dropIndex(['support_technician_id']);
            $table->dropColumn('support_technician_id');
        });
    }
};
