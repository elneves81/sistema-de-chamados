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
            // Campos para integração LDAP
            $table->string('ldap_dn')->nullable()->unique()->comment('Distinguished Name do LDAP');
            $table->string('ldap_upn')->nullable()->comment('User Principal Name do LDAP');
            $table->boolean('auth_via_ldap')->default(false)->comment('Autenticação via LDAP habilitada');
            
            // Índices para performance
            $table->index('ldap_dn');
            $table->index(['auth_via_ldap', 'is_active']);
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
            $table->dropIndex(['users_ldap_dn_index']);
            $table->dropIndex(['users_auth_via_ldap_is_active_index']);
            $table->dropUnique(['users_ldap_dn_unique']);
            $table->dropColumn(['ldap_dn', 'ldap_upn', 'auth_via_ldap']);
        });
    }
};
