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
        // Verifica se a coluna já existe para evitar erro em produção
        if (!Schema::hasColumn('tickets', 'resolution_time')) {
            Schema::table('tickets', function (Blueprint $table) {
                // Tempo de resolução em horas (calculado automaticamente ou manualmente)
                $table->decimal('resolution_time', 8, 2)->nullable()->after('resolved_at')
                    ->comment('Tempo de resolução em horas');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('tickets', 'resolution_time')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('resolution_time');
            });
        }
    }
};
