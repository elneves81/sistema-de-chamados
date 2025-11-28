<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela para armazenar histórico de classificações da IA
        if (!Schema::hasTable('ai_classifications')) {
        Schema::create('ai_classifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            
            // Classificações sugeridas
            $table->foreignId('suggested_category_id')->nullable()->constrained('categories');
            $table->decimal('category_confidence', 5, 4)->nullable();
            $table->enum('suggested_priority', ['low', 'medium', 'high', 'urgent'])->nullable();
            $table->decimal('priority_confidence', 5, 4)->nullable();
            $table->foreignId('suggested_technician_id')->nullable()->constrained('users');
            $table->decimal('technician_confidence', 5, 4)->nullable();
            
            // Classificações reais (feedback para treino)
            $table->foreignId('actual_category_id')->nullable()->constrained('categories');
            $table->enum('actual_priority', ['low', 'medium', 'high', 'urgent'])->nullable();
            $table->foreignId('actual_technician_id')->nullable()->constrained('users');
            
            // Métricas de acurácia
            $table->boolean('category_correct')->nullable();
            $table->boolean('priority_correct')->nullable();
            $table->boolean('technician_correct')->nullable();
            
            // Dados usados na classificação
            $table->json('features_used')->nullable();
            $table->string('model_version')->nullable();
            $table->integer('processing_time_ms')->nullable();
            
            $table->timestamps();
            
            $table->index(['ticket_id', 'created_at']);
            $table->index('category_correct');
        });
        }

        // Tabela para tickets similares (recomendação de soluções)
        if (!Schema::hasTable('similar_tickets')) {
        Schema::create('similar_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('similar_ticket_id')->constrained('tickets')->onDelete('cascade');
            
            $table->decimal('similarity_score', 5, 4);
            $table->json('similarity_factors')->nullable();
            
            $table->boolean('was_helpful')->nullable();
            $table->timestamp('suggested_at');
            
            $table->timestamps();
            
            $table->index(['ticket_id', 'similarity_score']);
            $table->unique(['ticket_id', 'similar_ticket_id']);
        });
        }

        // Tabela para especialização de técnicos (aprendizado)
        if (!Schema::hasTable('technician_expertise')) {
        Schema::create('technician_expertise', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            
            $table->integer('tickets_resolved')->default(0);
            $table->decimal('avg_resolution_time_hours', 8, 2)->nullable();
            $table->decimal('first_time_fix_rate', 5, 4)->nullable();
            $table->decimal('customer_satisfaction', 5, 4)->nullable();
            $table->integer('reopen_count')->default(0);
            
            $table->decimal('expertise_score', 5, 4)->default(0);
            
            $table->timestamp('last_calculated_at')->nullable();
            
            $table->timestamps();
            
            $table->unique(['user_id', 'category_id']);
            $table->index('expertise_score');
        });
        }

        // Tabela para palavras-chave e padrões identificados
        if (!Schema::hasTable('ticket_keywords')) {
        Schema::create('ticket_keywords', function (Blueprint $table) {
            $table->id();
            $table->string('keyword', 100);
            $table->foreignId('category_id')->nullable()->constrained('categories');
            
            $table->integer('frequency')->default(1);
            $table->decimal('category_correlation', 5, 4)->nullable();
            $table->decimal('priority_weight', 5, 4)->default(0.5);
            
            $table->timestamps();
            
            $table->unique(['keyword', 'category_id']);
            $table->index('frequency');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_keywords');
        Schema::dropIfExists('technician_expertise');
        Schema::dropIfExists('similar_tickets');
        Schema::dropIfExists('ai_classifications');
    }
};
