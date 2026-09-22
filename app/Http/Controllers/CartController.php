<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use App\Support\Locale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function index(): View
    {
        // peek() — sepeti yalnızca görüntülemek için boş kayıt açılmasın.
        return view('pages.cart', [
            'cart' => $this->cart->peek(),
            'subtotal' => $this->cart->subtotal(),
            'problems' => $this->cart->problems(),
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $data = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:'.config('commerce.cart.max_quantity')],
        ]);

        if (! $product->isInStock()) {
            return back()->with('cart.error', __('site.cart.out_of_stock'));
        }

        $this->cart->add($product, (int) ($data['quantity'] ?? 1));

        return back()->with('cart.added', $product->name);
    }

    public function update(Request $request, CartItem $item): RedirectResponse
    {
        $this->authorizeItem($item);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:'.config('commerce.cart.max_quantity')],
        ]);

        $this->cart->update($item, (int) $data['quantity']);

        return redirect(Locale::route('cart'));
    }

    public function destroy(CartItem $item): RedirectResponse
    {
        $this->authorizeItem($item);
        $this->cart->remove($item);

        return redirect(Locale::route('cart'));
    }

    /** Kalem gerçekten bu ziyaretçinin sepetinde mi? */
    protected function authorizeItem(CartItem $item): void
    {
        $cart = $this->cart->peek();

        abort_unless($cart && $item->cart_id === $cart->id, 403);
    }
}
