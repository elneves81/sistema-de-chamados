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
            // Adicionar campos de resolução se não existirem
            if (!Schema::hasColumn('tickets', 'resolved_by')) {
                $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null')->after('resolved_at');
            }
            if (!Schema::hasColumn('tickets', 'closed_by')) {
                $table->foreignId('closed_by')->nullable()->constrained('users')->onDelete('set null')->after('closed_at');
            }
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
            // Remover as colunas se existirem
            if (Schema::hasColumn('tickets', 'resolved_by')) {
                $table->dropForeign(['resolved_by']);
                $table->dropColumn('resolved_by');
            }
            if (Schema::hasColumn('tickets', 'closed_by')) {
                $table->dropForeign(['closed_by']);
                $table->dropColumn('closed_by');
            }
        });
    }
};
