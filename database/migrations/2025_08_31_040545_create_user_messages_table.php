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
        Schema::create('user_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_user_id')->comment('ID do usuário que enviou');
            $table->unsignedBigInteger('to_user_id')->comment('ID do usuário que recebeu');
            $table->string('subject')->comment('Assunto da mensagem');
            $table->text('message')->comment('Conteúdo da mensagem');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->comment('Prioridade da mensagem');
            $table->boolean('is_read')->default(false)->comment('Se foi lida pelo destinatário');
            $table->boolean('email_sent')->default(false)->comment('Se email de notificação foi enviado');
            $table->timestamp('read_at')->nullable()->comment('Quando foi lida');
            $table->timestamp('email_sent_at')->nullable()->comment('Quando email foi enviado');
            $table->json('attachments')->nullable()->comment('Anexos da mensagem');
            $table->timestamps();

            // Foreign keys
            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index(['to_user_id', 'is_read']);
            $table->index(['from_user_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_messages');
    }
};
