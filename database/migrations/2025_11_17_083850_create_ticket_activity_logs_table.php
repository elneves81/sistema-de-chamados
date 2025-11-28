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
        Schema::create('ticket_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Quem fez a ação
            $table->foreignId('target_user_id')->nullable()->constrained('users')->onDelete('set null'); // Usuário afetado (ex: atribuído a)
            $table->string('action'); // created, updated, assigned, transferred, commented, status_changed, etc
            $table->text('description'); // Descrição da ação em português
            $table->json('changes')->nullable(); // Detalhes das mudanças (antes/depois)
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['ticket_id', 'created_at']);
            $table->index('user_id');
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_activity_logs');
    }
};
