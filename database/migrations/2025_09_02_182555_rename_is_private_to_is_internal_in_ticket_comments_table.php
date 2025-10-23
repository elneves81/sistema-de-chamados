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
        Schema::table('ticket_comments', function (Blueprint $table) {
            // Renomear a coluna is_private para is_internal
            $table->renameColumn('is_private', 'is_internal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_comments', function (Blueprint $table) {
            // Reverter a mudança - renomear is_internal de volta para is_private
            $table->renameColumn('is_internal', 'is_private');
        });
    }
};
