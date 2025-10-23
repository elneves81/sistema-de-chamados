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
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('subject');
            $table->text('message');
            $table->enum('type', ['suporte', 'duvida', 'sugestao', 'emergencia']);
            $table->enum('status', ['pendente', 'em_andamento', 'resolvido', 'arquivado'])->default('pendente');
            $table->unsignedBigInteger('assigned_to')->nullable(); // Admin responsável
            $table->unsignedBigInteger('user_id')->nullable(); // Se usuário logado
            $table->timestamp('responded_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['status', 'type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_messages');
    }
};
