<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();                       // CN-2026-0001
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('status')->default('pending');             // pending|confirmed|preparing|ready|shipped|completed|cancelled
            $table->string('payment_status')->default('unpaid');      // unpaid|paid|refunded
            $table->string('payment_method');                         // transfer|on_delivery|card
            $table->string('shipping_method');                        // pickup|delivery
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();

            // Tutarların tamamı KURUŞ cinsinden tam sayı.
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('shipping_total')->default(0);
            $table->unsignedBigInteger('discount_total')->default(0);
            $table->unsignedBigInteger('grand_total')->default(0);
            $table->string('currency', 3)->default('TRY');

            // Müşteri ve adres, sipariş anındaki haliyle kopyalanır — sonradan
            // adres silinse ya da değişse sipariş kaydı bozulmasın.
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone');
            $table->json('shipping_address')->nullable();

            $table->text('note')->nullable();
            $table->string('locale', 5)->default('tr');
            $table->ipAddress('ip')->nullable();

            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            // Ürün adı/fiyatı sipariş anındaki haliyle saklanır.
            $table->string('name');
            $table->string('sku')->nullable();
            $table->string('unit')->nullable();
            $table->unsignedBigInteger('unit_price');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('line_total');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
