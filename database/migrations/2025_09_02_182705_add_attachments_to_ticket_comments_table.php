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
            // Adicionar coluna de anexos se não existir
            if (!Schema::hasColumn('ticket_comments', 'attachments')) {
                $table->json('attachments')->nullable()->after('comment');
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
        Schema::table('ticket_comments', function (Blueprint $table) {
            // Remover a coluna attachments se existir
            if (Schema::hasColumn('ticket_comments', 'attachments')) {
                $table->dropColumn('attachments');
            }
        });
    }
};
