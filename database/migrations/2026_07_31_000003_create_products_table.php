<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('sku')->nullable()->unique();
            $table->json('name');
            $table->json('summary')->nullable();
            $table->json('description')->nullable();

            // Fiyat kuruş cinsinden tam sayı tutulur — kayan nokta yuvarlama
            // hatası para hesabına hiç bulaşmasın.
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('compare_price')->nullable();
            $table->string('currency', 3)->default('TRY');
            $table->string('unit')->nullable();          // m², adet, takım

            $table->integer('stock')->default(0);
            $table->boolean('track_stock')->default(true);
            $table->boolean('allow_backorder')->default(false);

            $table->json('images')->nullable();          // yol dizisi
            $table->json('specs')->nullable();           // [{label, value}]

            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['is_active', 'category_id', 'position']);
            $table->index(['is_active', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
