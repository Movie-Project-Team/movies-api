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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->text('name');
            $table->text('slug');
            $table->text('description');
            $table->text('thumbnail');
            $table->text('poster');
            $table->string('time');
            $table->string('esp_current');
            $table->string('esp_total');
            $table->string('type');
            $table->string('season');
            $table->string('vote_average');
            $table->string('vote_count');
            $table->string('status');
            $table->string('quality');
            $table->string('lang');
            $table->integer('year');
            $table->integer('view');
            $table->text('IMDb')->nullable();
            $table->text('trailer')->nullable();
            $table->text('produce_by')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
