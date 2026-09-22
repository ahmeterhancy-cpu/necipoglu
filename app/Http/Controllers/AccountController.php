<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Support\Locale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        return view('pages.account.index', [
            'user' => $user,
            'orders' => $user->orders()->withCount('items')->take(5)->get(),
            'addressCount' => $user->addresses()->count(),
        ]);
    }

    /* ── Siparişler ───────────────────────────────────────────────────── */

    public function orders(): View
    {
        return view('pages.account.orders', [
            'orders' => Auth::user()->orders()->withCount('items')->paginate(15),
        ]);
    }

    public function order(Order $order): View
    {
        abort_unless($order->user_id === Auth::id(), 404);

        return view('pages.account.order', [
            'order' => $order->load('items', 'branch'),
            'bankAccounts' => config('commerce.bank_accounts'),
        ]);
    }

    public function cancelOrder(Order $order): RedirectResponse
    {
        abort_unless($order->user_id === Auth::id(), 404);

        if (! $order->isCancellable()) {
            return back()->with('account.error', __('site.order.cannot_cancel'));
        }

        // İptalde stok geri yüklenir — sipariş verilirken düşülmüştü.
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                if ($item->product?->track_stock) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => 'cancelled', 'cancelled_at' => now()]);
        });

        return back()->with('account.done', __('site.order.cancelled'));
    }

    /* ── Adresler ─────────────────────────────────────────────────────── */

    public function addresses(): View
    {
        return view('pages.account.addresses', [
            'addresses' => Auth::user()->addresses()->latest()->get(),
        ]);
    }

    public function storeAddress(Request $request): RedirectResponse
    {
        $data = $this->validateAddress($request);

        DB::transaction(function () use ($data) {
            if ($data['is_default'] ?? false) {
                Auth::user()->addresses()->update(['is_default' => false]);
            }

            Auth::user()->addresses()->create($data);
        });

        return redirect(Locale::route('account.addresses'))->with('account.done', __('site.account.address_saved'));
    }

    public function updateAddress(Request $request, Address $address): RedirectResponse
    {
        abort_unless($address->user_id === Auth::id(), 404);

        $data = $this->validateAddress($request);

        DB::transaction(function () use ($data, $address) {
            if ($data['is_default'] ?? false) {
                Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            }

            $address->update($data);
        });

        return redirect(Locale::route('account.addresses'))->with('account.done', __('site.account.address_saved'));
    }

    public function destroyAddress(Address $address): RedirectResponse
    {
        abort_unless($address->user_id === Auth::id(), 404);

        $address->delete();

        return redirect(Locale::route('account.addresses'))->with('account.done', __('site.account.address_deleted'));
    }

    protected function validateAddress(Request $request): array
    {
        return $request->validate([
            'title' => ['nullable', 'string', 'max:60'],
            'full_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'city' => ['required', 'string', 'max:80'],
            'district' => ['nullable', 'string', 'max:80'],
            'line' => ['required', 'string', 'max:400'],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }
}
