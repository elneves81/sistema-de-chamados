<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocalToTicketsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('tickets') && !Schema::hasColumn('tickets', 'local')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->string('local')->nullable()->after('title');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('tickets', 'local')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('local');
            });
        }
    }
}
