<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Resolver duplicatas de username adicionando sufixo
        $duplicates = DB::select("
            SELECT username, GROUP_CONCAT(id ORDER BY id) as ids
            FROM users 
            WHERE username IS NOT NULL
            GROUP BY username 
            HAVING COUNT(*) > 1
        ");
        
        foreach ($duplicates as $duplicate) {
            $ids = explode(',', $duplicate->ids);
            // Mantém o primeiro, adiciona sufixo nos demais
            array_shift($ids); // Remove o primeiro ID
            foreach ($ids as $index => $id) {
                DB::table('users')
                    ->where('id', $id)
                    ->update(['username' => $duplicate->username . '_' . ($index + 1)]);
            }
        }
        
        Schema::table('users', function (Blueprint $table) {
            // Tornar email nullable (índice já existe)
            $table->string('email')->nullable()->change();
            
            // Tornar username obrigatório e único
            $table->string('username')->nullable(false)->unique()->change();
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
            // Reverter mudanças
            $table->string('email')->nullable(false)->change();
            
            $table->dropUnique(['username']);
            $table->string('username')->nullable()->change();
        });
    }
};
