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
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('patrimonio')->unique()->comment('Número de patrimônio');
            $table->string('numero_serie')->unique()->comment('Número de série');
            $table->string('modelo')->comment('Modelo da máquina');
            $table->string('marca')->nullable()->comment('Marca/Fabricante');
            $table->string('tipo')->default('desktop')->comment('Tipo: desktop, notebook, servidor, impressora');
            $table->text('descricao')->nullable()->comment('Descrição adicional');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->comment('Usuário vinculado');
            $table->string('processador')->nullable();
            $table->string('memoria_ram')->nullable();
            $table->string('armazenamento')->nullable();
            $table->string('sistema_operacional')->nullable();
            $table->date('data_aquisicao')->nullable()->comment('Data de aquisição');
            $table->decimal('valor_aquisicao', 10, 2)->nullable()->comment('Valor de aquisição');
            $table->enum('status', ['ativo', 'inativo', 'manutencao', 'descartado'])->default('ativo');
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machines');
    }
};
