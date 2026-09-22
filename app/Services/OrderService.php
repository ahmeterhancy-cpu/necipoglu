<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Siparişin oluşturulduğu tek yer.
 *
 * Tamamı tek bir transaction içinde döner: stok satırları kilitlenir,
 * yeterlilik yeniden denetlenir, sipariş yazılır ve stok düşülür. Böylece
 * aynı anda gelen iki sipariş son ürünü birlikte satamaz.
 */
class OrderService
{
    public function __construct(protected CartService $cart) {}

    /**
     * @param  array{customer_name:string, customer_email:?string, customer_phone:string,
     *               shipping_method:string, payment_method:string, branch_id:?int,
     *               shipping_address:?array, note:?string}  $data
     *
     * @throws RuntimeException stok yetmiyorsa ya da sepet boşsa
     */
    public function place(array $data): Order
    {
        $cart = $this->cart->current();

        if ($cart->isEmpty()) {
            throw new RuntimeException(__('site.cart.empty'));
        }

        $shippingMethod = $data['shipping_method'];
        $shippingCost = $this->cart->shippingCost($shippingMethod);

        // Kalemleri transaction dışında topla; içeride yalnızca kilit ve yazma olsun.
        $lines = $cart->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
        ])->all();

        $order = DB::transaction(function () use ($data, $lines, $shippingMethod, $shippingCost) {
            $ids = array_column($lines, 'product_id');

            // Satırları kilitle — eşzamanlı siparişler sırayla geçsin.
            $products = Product::whereIn('id', $ids)->lockForUpdate()->get()->keyBy('id');

            $subtotal = 0;
            $items = [];

            foreach ($lines as $line) {
                $product = $products->get($line['product_id']);

                if (! $product || ! $product->is_active) {
                    throw new RuntimeException(__('site.cart.gone', ['name' => $product?->name ?? '—']));
                }

                if ($product->track_stock && ! $product->allow_backorder && $product->stock < $line['quantity']) {
                    throw new RuntimeException(__('site.cart.short_stock', [
                        'name' => $product->name,
                        'stock' => max(0, $product->stock),
                    ]));
                }

                $lineTotal = $product->price * $line['quantity'];
                $subtotal += $lineTotal;

                $items[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'unit' => $product->unit,
                    'unit_price' => $product->price,
                    'quantity' => $line['quantity'],
                    'line_total' => $lineTotal,
                ];
            }

            $order = $this->createWithNumber([
                'user_id' => Auth::id(),
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $data['payment_method'],
                'shipping_method' => $shippingMethod,
                'branch_id' => $data['branch_id'] ?? null,
                'subtotal' => $subtotal,
                'shipping_total' => $shippingCost,
                'discount_total' => 0,
                'grand_total' => $subtotal + $shippingCost,
                'currency' => config('commerce.currency'),
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'],
                'shipping_address' => $data['shipping_address'] ?? null,
                'note' => $data['note'] ?? null,
                'locale' => app()->getLocale(),
                'ip' => request()->ip(),
            ]);

            $order->items()->createMany($items);

            // Stok düş — yalnızca takip edilen ürünlerde.
            foreach ($items as $item) {
                $product = $products->get($item['product_id']);

                if ($product->track_stock) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            return $order;
        });

        $this->cart->clear();

        return $order;
    }

    /**
     * Sipariş numarası yıl içinde sıralıdır. Eşzamanlı iki siparişte aynı
     * numara üretilirse unique kısıt patlar; birkaç kez yeniden denenir.
     */
    protected function createWithNumber(array $attributes): Order
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            try {
                return Order::create($attributes + ['number' => Order::nextNumber()]);
            } catch (QueryException $e) {
                if (! str_contains(strtolower($e->getMessage()), 'unique')) {
                    throw $e;
                }
            }
        }

        throw new RuntimeException('Sipariş numarası üretilemedi.');
    }
}
