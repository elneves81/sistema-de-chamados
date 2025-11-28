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
        Schema::table('users', function (Blueprint $table) {
            // Canais de contato (phone já existe, adicionamos telegram e whatsapp)
            $table->string('telegram_id')->nullable()->after('phone');
            $table->string('whatsapp', 20)->nullable()->after('telegram_id');
            
            // Preferências de notificação (JSON)
            $table->json('notification_preferences')->nullable()->after('whatsapp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['telegram_id', 'whatsapp', 'notification_preferences']);
        });
    }
};
