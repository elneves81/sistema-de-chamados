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
        Schema::create('ai_classifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id')->nullable();
            $table->string('title');
            $table->text('description');
            $table->string('suggested_category')->nullable();
            $table->unsignedBigInteger('suggested_category_id')->nullable();
            $table->string('detected_priority'); // low, medium, high
            $table->decimal('confidence_score', 5, 2); // 0.00 to 100.00
            $table->json('keywords');
            $table->json('analysis_result'); // detailed analysis
            $table->boolean('was_accepted')->nullable();
            $table->string('human_category')->nullable(); // categoria escolhida pelo humano
            $table->string('human_priority')->nullable(); // prioridade escolhida pelo humano
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('suggested_category_id')->references('id')->on('categories')->onDelete('set null');
            $table->index(['detected_priority', 'confidence_score']);
            $table->index(['was_accepted']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ai_classifications');
    }
};
