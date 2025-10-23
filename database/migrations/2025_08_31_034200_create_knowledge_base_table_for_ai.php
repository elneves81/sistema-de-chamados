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
        // Criar tabela knowledge_base se não existir (compatível com o modelo KnowledgeBase)
        if (!Schema::hasTable('knowledge_base')) {
            Schema::create('knowledge_base', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('content');
                $table->text('excerpt')->nullable();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->unsignedBigInteger('author_id');
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
                $table->boolean('is_public')->default(true);
                $table->boolean('is_featured')->default(false);
                $table->json('tags')->nullable();
                $table->integer('views')->default(0);
                $table->timestamp('published_at')->nullable();
                $table->timestamps();

                $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
                $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['status', 'is_public']);
                $table->index(['category_id', 'status']);
                $table->fullText(['title', 'content', 'excerpt']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('knowledge_base');
    }
};
