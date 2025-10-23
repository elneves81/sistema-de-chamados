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
            $table->timestamp('ldap_last_sync')->nullable()->after('ldap_dn');
            $table->string('ldap_user_account_control')->nullable()->after('ldap_last_sync');
            $table->boolean('ldap_is_active')->default(true)->after('ldap_user_account_control');
            $table->timestamp('ldap_created_at')->nullable()->after('ldap_is_active');
            $table->timestamp('ldap_updated_at')->nullable()->after('ldap_created_at');
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
            $table->dropColumn([
                'ldap_last_sync',
                'ldap_user_account_control', 
                'ldap_is_active',
                'ldap_created_at',
                'ldap_updated_at'
            ]);
        });
    }
};
