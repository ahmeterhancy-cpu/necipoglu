<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Markanın ürün grupları. Dil başına kısa etiketlerden oluşan bir liste:
     *   {"tr": ["El duşları", "Tepe duşları"], "en": ["Hand showers", ...]}
     *
     * HasTranslations dize taşımak için yazıldı, liste için değil; bu yüzden
     * çeviri sütunu değil düz JSON tutuluyor ve modelde okunuyor.
     */
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->json('highlights')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('highlights');
        });
    }
};
