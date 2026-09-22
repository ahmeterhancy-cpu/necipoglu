<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Referans projeler. Tablo adı "references" değil — REFERENCES,
     * MySQL'de ayrılmış bir anahtar kelimedir.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('title');
            $table->json('summary')->nullable();
            $table->json('body')->nullable();
            $table->string('client')->nullable();
            $table->string('location')->nullable();
            $table->year('year')->nullable();
            $table->json('scope')->nullable();           // kullanılan ürün grupları
            $table->string('cover')->nullable();
            $table->json('gallery')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['is_active', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
