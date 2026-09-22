<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Sepetin tek sahibi. Oturum/kullanıcı eşlemesi, ekleme, güncelleme ve
 * toplamlar buradan geçer; controller'lar sepet mantığı taşımaz.
 */
class CartService
{
    protected const SESSION_KEY = 'cart_token';

    protected ?Cart $cart = null;

    /** Geçerli sepet; yoksa oluşturulur. */
    public function current(): Cart
    {
        if ($this->cart) {
            return $this->cart;
        }

        $cart = Auth::check() ? $this->forUser() : $this->forGuest();

        return $this->cart = $cart->load('items.product.category', 'items.product.brand');
    }

    /** Var olan sepeti getirir ama yoksa OLUŞTURMAZ — okuma amaçlı. */
    public function peek(): ?Cart
    {
        if ($this->cart) {
            return $this->cart;
        }

        $cart = Auth::check()
            ? Cart::where('user_id', Auth::id())->first()
            : Cart::where('session_token', session(self::SESSION_KEY))->first();

        return $this->cart = $cart?->load('items.product');
    }

    protected function forUser(): Cart
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        // Giriş öncesi misafir sepeti varsa kullanıcı sepetine taşı.
        $token = session(self::SESSION_KEY);

        if ($token) {
            $guest = Cart::where('session_token', $token)->with('items')->first();

            if ($guest && $guest->isNot($cart)) {
                $this->merge($guest, $cart);
            }

            session()->forget(self::SESSION_KEY);
        }

        return $cart;
    }

    protected function forGuest(): Cart
    {
        $token = session(self::SESSION_KEY);

        if ($token && $cart = Cart::where('session_token', $token)->first()) {
            return $cart;
        }

        $token = Str::random(40);
        session([self::SESSION_KEY => $token]);

        return Cart::create(['session_token' => $token]);
    }

    /** Misafir sepetini kullanıcı sepetine kat; adetler toplanır. */
    protected function merge(Cart $from, Cart $into): void
    {
        foreach ($from->items as $item) {
            $existing = CartItem::where('cart_id', $into->id)
                ->where('product_id', $item->product_id)
                ->first();

            if ($existing) {
                $existing->update([
                    'quantity' => min(
                        $existing->quantity + $item->quantity,
                        config('commerce.cart.max_quantity'),
                    ),
                ]);
            } else {
                $item->update(['cart_id' => $into->id]);
            }
        }

        $from->delete();
        $into->load('items');
    }

    /* ── İşlemler ─────────────────────────────────────────────────────── */

    public function add(Product $product, int $quantity = 1): CartItem
    {
        $cart = $this->current();
        $max = (int) config('commerce.cart.max_quantity');

        $item = CartItem::firstOrNew([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);

        $item->quantity = min(max(1, ($item->quantity ?? 0) + $quantity), $max);
        $item->save();

        $this->cart = null;   // toplamlar yeniden okunsun

        return $item;
    }

    public function update(CartItem $item, int $quantity): void
    {
        $max = (int) config('commerce.cart.max_quantity');

        if ($quantity < 1) {
            $item->delete();
        } else {
            $item->update(['quantity' => min($quantity, $max)]);
        }

        $this->cart = null;
    }

    public function remove(CartItem $item): void
    {
        $item->delete();
        $this->cart = null;
    }

    public function clear(): void
    {
        $cart = $this->peek();
        $cart?->items()->delete();
        $this->cart = null;
    }

    /* ── Toplamlar ────────────────────────────────────────────────────── */

    public function count(): int
    {
        return $this->peek()?->totalQuantity() ?? 0;
    }

    /** Okuma amaçlı — sepet yoksa OLUŞTURMAZ, sıfır döner. */
    public function subtotal(): int
    {
        return $this->peek()?->subtotal() ?? 0;
    }

    /** Seçilen teslimat yöntemine göre kargo bedeli, kuruş. */
    public function shippingCost(string $method): int
    {
        $config = config("commerce.shipping.{$method}");

        if (! $config) {
            return 0;
        }

        $freeOver = $config['free_over'] ?? null;

        if ($freeOver !== null && $this->subtotal() >= $freeOver) {
            return 0;
        }

        return (int) $config['price'];
    }

    public function total(string $shippingMethod): int
    {
        return $this->subtotal() + $this->shippingCost($shippingMethod);
    }

    /**
     * Sepetteki kalemlerin hâlâ satılabilir olup olmadığını denetler.
     * Ödeme adımına geçmeden önce çağrılır.
     *
     * @return array<int, string> sorunlu kalemlerin açıklamaları
     */
    public function problems(): array
    {
        $problems = [];
        $cart = $this->peek();

        if (! $cart) {
            return $problems;
        }

        foreach ($cart->items as $item) {
            $product = $item->product;

            if (! $product || ! $product->is_active) {
                $problems[] = __('site.cart.gone', ['name' => $product?->name ?? '—']);
                continue;
            }

            if ($product->track_stock && ! $product->allow_backorder && $product->stock < $item->quantity) {
                $problems[] = __('site.cart.short_stock', [
                    'name' => $product->name,
                    'stock' => max(0, $product->stock),
                ]);
            }
        }

        return $problems;
    }
}
