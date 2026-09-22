<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** CN TV — kurumsal video arşivi. */
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('title');
            $table->json('description')->nullable();
            $table->string('provider')->default('youtube');   // youtube | vimeo | file
            $table->string('video_id')->nullable();           // YouTube/Vimeo kimliği
            $table->string('file')->nullable();               // kendi sunucumuzdaki dosya
            $table->string('poster')->nullable();
            $table->unsignedInteger('duration')->nullable();  // saniye
            $table->json('category')->nullable();             // Tanıtım, Uygulama, Röportaj
            $table->date('published_at')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['is_active', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
