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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_code')->unique();
            $table->string('name')->nullable();
            $table->foreignId('host_id')->nullable()->constrained('profile')->nullOnDelete();
            $table->boolean('is_locked')->default(false);
            $table->string('password')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->foreignId('movie_id')->nullable()->constrained('movies')->cascadeOnDelete();
            $table->unsignedInteger('capacity')->default(8);
            $table->tinyInteger('status')->default(0)->comment('Trạng thái của phòng: 0 => pending, 1 => ongoing, 2 => finished');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
