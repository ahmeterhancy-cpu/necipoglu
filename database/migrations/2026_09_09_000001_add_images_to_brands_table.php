<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Marka sayfasındaki görsel şeridi. Logodaki yaklaşımın aynısı:
     * panelden yüklenebilir, panele girmeden klasöre de atılabilir.
     */
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->json('images')->nullable()->after('logo');
        });
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('images');
        });
    }
};
