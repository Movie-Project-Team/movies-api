<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crawl_movies_log', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->integer('total_movies')->default(0);
            $table->integer('success')->default(0);
            $table->integer('failed')->default(0);
            $table->decimal('success_rate', 5, 2)->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crawl_movies_log');
    }
};
