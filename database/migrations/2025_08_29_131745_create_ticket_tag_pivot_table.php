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
        Schema::create('ticket_tag_pivot', function (Blueprint $table) {
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('ticket_tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->primary(['ticket_id', 'ticket_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_tag_pivot');
    }
};
