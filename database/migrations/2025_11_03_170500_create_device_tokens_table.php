<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('device_tokens')) {
            Schema::create('device_tokens', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('platform', 32)->nullable(); // android, ios, web
                $table->string('token')->index();
                $table->json('device_info')->nullable();
                $table->timestamp('last_seen_at')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['user_id', 'token']);
            });
        } else {
            // Ensure columns exist if table was created manually
            Schema::table('device_tokens', function (Blueprint $table) {
                if (!Schema::hasColumn('device_tokens', 'platform')) {
                    $table->string('platform', 32)->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('device_tokens', 'device_info')) {
                    $table->json('device_info')->nullable()->after('token');
                }
                if (!Schema::hasColumn('device_tokens', 'last_seen_at')) {
                    $table->timestamp('last_seen_at')->nullable()->after('device_info');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('device_tokens')) {
            Schema::drop('device_tokens');
        }
    }
};
