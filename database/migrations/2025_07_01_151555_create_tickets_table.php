
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Título do chamado
            $table->string('local')->nullable(); // Local do chamado
            $table->text('description'); // Descrição detalhada
            $table->enum('status', ['open', 'in_progress', 'pending', 'resolved', 'closed'])->default('open');
            $table->enum('priority', ['low', 'normal', 'high', 'critical'])->default('normal');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Cliente que criou
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // Técnico responsável
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // Categoria
            $table->foreignId('asset_id')->nullable()->constrained()->onDelete('set null'); // Ativo relacionado
            $table->tinyInteger('impact')->default(2); // Impacto: 1-baixo, 2-médio, 3-alto
            $table->tinyInteger('urgency')->default(2); // Urgência: 1-baixo, 2-médio, 3-alto
            $table->timestamp('due_date')->nullable(); // Prazo SLA
            $table->timestamp('resolved_at')->nullable(); // Data de resolução
            $table->timestamp('closed_at')->nullable(); // Data de fechamento
            $table->json('attachments')->nullable(); // Anexos
            $table->timestamps();
            // Índices para performance
            $table->index(['status', 'priority']);
            $table->index(['user_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['asset_id']);
            $table->index(['impact', 'urgency']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
