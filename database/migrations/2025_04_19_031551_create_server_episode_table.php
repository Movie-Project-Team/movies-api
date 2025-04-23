
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
        Schema::create('server_episode', function (Blueprint $table) {
            $table->id();
            $table->foreignId('episode_id')->references('id')->on('episodes')->onDelete('cascade');
            $table->foreignId('server_id')->references('id')->on('server')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('filename');
            $table->string('link_download');
            $table->string('link_watch');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_episode');
    }
};