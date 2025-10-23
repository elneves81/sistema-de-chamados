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
            // Verificar se as colunas já existem antes de adicionar
            if (!Schema::hasColumn('tickets', 'location_id')) {
                $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null')->after('category_id');
            }
            
            if (!Schema::hasColumn('tickets', 'local')) {
                $table->string('local')->nullable()->after('location_id');
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
            if (Schema::hasColumn('tickets', 'location_id')) {
                $table->dropForeign(['location_id']);
                $table->dropColumn('location_id');
            }
            
            if (Schema::hasColumn('tickets', 'local')) {
                $table->dropColumn('local');
            }
        });
    }
};
