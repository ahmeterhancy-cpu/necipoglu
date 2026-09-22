<?php

namespace App\Http\Controllers;

use App\Mail\OrderPlaced;
use App\Models\Branch;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use App\Support\Locale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cart,
        protected OrderService $orders,
    ) {}

    public function index(): View|RedirectResponse
    {
        $cart = $this->cart->peek();

        if (! $cart || $cart->isEmpty()) {
            return redirect(Locale::route('cart'));
        }

        return view('pages.checkout', [
            'cart' => $cart,
            'subtotal' => $this->cart->subtotal(),
            'problems' => $this->cart->problems(),
            'branches' => Branch::query()->active()->ordered()->get(),
            'shippingMethods' => config('commerce.shipping'),
            'paymentMethods' => collect(config('commerce.payments'))->filter(fn ($m) => $m['enabled'])->all(),
            'addresses' => Auth::check() ? Auth::user()->addresses()->latest()->get() : collect(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $shippingKeys = array_keys(config('commerce.shipping'));
        $paymentKeys = array_keys(collect(config('commerce.payments'))->filter(fn ($m) => $m['enabled'])->all());

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['nullable', 'email', 'max:160'],
            'customer_phone' => ['required', 'string', 'max:40'],
            'shipping_method' => ['required', Rule::in($shippingKeys)],
            'payment_method' => ['required', Rule::in($paymentKeys)],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'city' => ['nullable', 'string', 'max:80'],
            'district' => ['nullable', 'string', 'max:80'],
            'line' => ['nullable', 'string', 'max:400'],
            'note' => ['nullable', 'string', 'max:2000'],
            'website' => ['prohibited'],   // bal küpü
        ], [
            'website.prohibited' => __('site.contact.spam'),
        ]);

        $needsAddress = config("commerce.shipping.{$data['shipping_method']}.needs_address");

        // Adrese teslimatta adres alanları zorunlu; şubeden teslimde şube.
        if ($needsAddress) {
            $request->validate([
                'city' => ['required', 'string', 'max:80'],
                'line' => ['required', 'string', 'max:400'],
            ]);
        } else {
            $request->validate(['branch_id' => ['required', 'exists:branches,id']]);
        }

        try {
            $order = $this->orders->place([
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'],
                'shipping_method' => $data['shipping_method'],
                'payment_method' => $data['payment_method'],
                'branch_id' => $needsAddress ? null : $data['branch_id'],
                'shipping_address' => $needsAddress ? [
                    'full_name' => $data['customer_name'],
                    'phone' => $data['customer_phone'],
                    'city' => $data['city'],
                    'district' => $data['district'] ?? null,
                    'line' => $data['line'],
                ] : null,
                'note' => $data['note'] ?? null,
            ]);
        } catch (RuntimeException $e) {
            return back()->withInput()->with('checkout.error', $e->getMessage());
        }

        // Sipariş numarası oturuma yazılır ki teşekkür sayfası, giriş
        // yapmamış müşteriye de yalnızca kendi siparişini gösterebilsin.
        session()->push('orders.own', $order->number);

        // E-posta gönderimi siparişi bloklamamalı: posta sunucusu düşse bile
        // müşteri teşekkür sayfasını görsün, sipariş kaydı sağlam kalsın.
        if ($order->customer_email) {
            try {
                Mail::to($order->customer_email)->send(new OrderPlaced($order));
            } catch (Throwable $e) {
                report($e);
            }
        }

        return redirect(Locale::route('checkout.success', $order));
    }

    public function success(Order $order): View
    {
        abort_unless($this->canView($order), 404);

        return view('pages.checkout-success', [
            'order' => $order->load('items', 'branch'),
            'bankAccounts' => config('commerce.bank_accounts'),
        ]);
    }

    /** Sipariş sahibi mi? Girişli kullanıcı ya da aynı oturumdan veren misafir. */
    protected function canView(Order $order): bool
    {
        if (Auth::check() && $order->user_id === Auth::id()) {
            return true;
        }

        return in_array($order->number, session('orders.own', []), true);
    }
}
