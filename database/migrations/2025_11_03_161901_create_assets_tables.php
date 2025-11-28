<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela de tipos de ativos
        if (!Schema::hasTable('asset_types')) {
        Schema::create('asset_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('requires_serial')->default(true);
            $table->json('custom_fields')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        }

        // Tabela de fabricantes
        if (!Schema::hasTable('manufacturers')) {
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('website')->nullable();
            $table->string('support_phone')->nullable();
            $table->string('support_email')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        }

        // Tabela de modelos de ativos
        if (!Schema::hasTable('asset_models')) {
        Schema::create('asset_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_type_id')->constrained('asset_types');
            $table->foreignId('manufacturer_id')->constrained('manufacturers');
            $table->string('name');
            $table->string('model_number')->nullable();
            $table->text('specifications')->nullable();
            $table->integer('warranty_months')->default(12);
            $table->decimal('average_lifespan_years', 4, 1)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        }

        // Tabela principal de ativos
        if (!Schema::hasTable('assets')) {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag')->unique(); // Etiqueta de patrimônio
            $table->string('qr_code')->unique()->nullable(); // QR Code gerado
            $table->foreignId('asset_type_id')->constrained('asset_types');
            $table->foreignId('asset_model_id')->constrained('asset_models');
            $table->foreignId('location_id')->nullable()->constrained('locations');
            
            // Informações do ativo
            $table->string('serial_number')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance', 'retired', 'lost'])->default('active');
            
            // Datas importantes
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->date('last_maintenance')->nullable();
            $table->date('next_maintenance')->nullable();
            
            // Valores
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->decimal('current_value', 10, 2)->nullable();
            
            // Atribuição
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->timestamp('assigned_at')->nullable();
            
            // Campos customizáveis
            $table->json('custom_data')->nullable();
            $table->json('photos')->nullable();
            
            // Notas e observações
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('asset_tag');
            $table->index('serial_number');
            $table->index('status');
            $table->index('warranty_expiry');
        });
        }

        // Histórico de manutenção
        if (!Schema::hasTable('asset_maintenances')) {
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->foreignId('ticket_id')->nullable()->constrained('tickets');
            $table->foreignId('performed_by')->nullable()->constrained('users');
            
            $table->enum('type', ['preventive', 'corrective', 'inspection', 'calibration', 'upgrade']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('work_performed')->nullable();
            
            $table->date('scheduled_date')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->decimal('cost', 10, 2)->nullable();
            $table->integer('downtime_minutes')->nullable();
            
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            
            $table->json('parts_used')->nullable();
            $table->json('attachments')->nullable();
            
            $table->timestamps();
            
            $table->index('scheduled_date');
            $table->index('status');
        });
        }

        // Vincular tickets a ativos
        if (Schema::hasTable('assets') && !Schema::hasColumn('tickets', 'asset_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->foreignId('asset_id')->nullable()->after('location_id')->constrained('assets');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tickets', 'asset_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropForeign(['asset_id']);
                $table->dropColumn('asset_id');
            });
        }
        
        Schema::dropIfExists('asset_maintenances');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_models');
        Schema::dropIfExists('manufacturers');
        Schema::dropIfExists('asset_types');
    }
};
