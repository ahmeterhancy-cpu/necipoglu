<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

/**
 * Her dil için bir kez çalıştırılır. $locale kaydedilen dilin kodudur;
 * URL segmentleri bu sayede dile göre değişir (/hakkimizda ↔ /en/about).
 */
return function (string $locale): void {
    /** Dile göre URL segmenti seçer. */
    $seg = fn (array $map) => $map[$locale] ?? reset($map);

    Route::get('/', [PageController::class, 'home'])->name('home');

    Route::get($seg(['tr' => 'hakkimizda', 'en' => 'about']), [PageController::class, 'about'])
        ->name('about');

    Route::get($seg(['tr' => 'istiraklerimiz', 'en' => 'group']), [PageController::class, 'subsidiaries'])
        ->name('subsidiaries');

    Route::get($seg(['tr' => 'yapi-market', 'en' => 'building-market']), [PageController::class, 'market'])
        ->name('market');

    /* — Mağaza ————————————————————————————————————————————————— */

    $shop = $seg(['tr' => 'urunler', 'en' => 'products']);

    Route::get($shop, [ShopController::class, 'index'])->name('shop.index');
    Route::get($shop.'/{category:slug}', [ShopController::class, 'category'])->name('shop.category');

    Route::get($seg(['tr' => 'urun', 'en' => 'product']).'/{product:slug}', [ShopController::class, 'product'])
        ->name('shop.product');

    /* — Kurumsal ——————————————————————————————————————————————— */

    $brands = $seg(['tr' => 'markalar', 'en' => 'brands']);

    Route::get($brands, [BrandController::class, 'index'])->name('brands');
    Route::get($brands.'/{brand:slug}', [BrandController::class, 'show'])->name('brands.show');

    $refs = $seg(['tr' => 'referanslar', 'en' => 'projects']);

    Route::get($refs, [ProjectController::class, 'index'])->name('projects.index');
    Route::get($refs.'/{project:slug}', [ProjectController::class, 'show'])->name('projects.show');

    Route::get('cn-tv', [VideoController::class, 'index'])->name('tv');

    Route::get('blog', [PostController::class, 'index'])->name('blog.index');
    Route::get('blog/{post:slug}', [PostController::class, 'show'])->name('blog.show');

    /* — İletişim ——————————————————————————————————————————————— */

    $contact = $seg(['tr' => 'iletisim', 'en' => 'contact']);

    Route::get($contact, [ContactController::class, 'index'])->name('contact');
    Route::post($contact, [ContactController::class, 'store'])
        ->middleware('throttle:8,1')
        ->name('contact.store');

    /* — Sipariş akışı ———————————————————————————————————————————
       Katalog modunda (config commerce.catalog) aşağıdaki yolların hepsi
       404 döner. Yollar kayıtlı kalır ki görünümlerdeki route() çağrıları
       kırılmasın; kapıyı ara katman tutar. */

    Route::middleware(\App\Http\Middleware\CatalogMode::class)->group(function () use ($seg) {
        /* — Sepet ————————————————————————————————————————————————— */

        $cart = $seg(['tr' => 'sepet', 'en' => 'cart']);

        Route::get($cart, [CartController::class, 'index'])->name('cart');

        // Daha özel yol önce tanımlanır ki {item} onu yutmasın.
        Route::post($cart.'/'.$seg(['tr' => 'ekle', 'en' => 'add']).'/{product:slug}', [CartController::class, 'store'])
            ->name('cart.store');

        Route::patch($cart.'/{item}', [CartController::class, 'update'])->name('cart.update');
        Route::delete($cart.'/{item}', [CartController::class, 'destroy'])->name('cart.destroy');

        /* — Ödeme ————————————————————————————————————————————————— */

        $checkout = $seg(['tr' => 'odeme', 'en' => 'checkout']);

        Route::get($checkout, [CheckoutController::class, 'index'])->name('checkout');
        Route::post($checkout, [CheckoutController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('checkout.store');

        Route::get($seg(['tr' => 'siparis', 'en' => 'order']).'/{order:number}', [CheckoutController::class, 'success'])
            ->name('checkout.success');

        /* — Üyelik ————————————————————————————————————————————————— */

        Route::middleware('guest')->group(function () use ($seg) {
            $login = $seg(['tr' => 'giris', 'en' => 'login']);
            $register = $seg(['tr' => 'kayit', 'en' => 'register']);

            Route::get($login, [AuthController::class, 'showLogin'])->name('login');
            Route::post($login, [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');

            Route::get($register, [AuthController::class, 'showRegister'])->name('register');
            Route::post($register, [AuthController::class, 'register'])->middleware('throttle:6,1')->name('register.store');
        });

        Route::post($seg(['tr' => 'cikis', 'en' => 'logout']), [AuthController::class, 'logout'])
            ->middleware('auth')
            ->name('logout');

        /* — Hesabım ——————————————————————————————————————————————— */

        $account = $seg(['tr' => 'hesabim', 'en' => 'account']);

        Route::middleware('auth')->group(function () use ($account, $seg) {
            Route::get($account, [AccountController::class, 'index'])->name('account');

            $orders = $seg(['tr' => 'siparislerim', 'en' => 'orders']);

            Route::get($account.'/'.$orders, [AccountController::class, 'orders'])->name('account.orders');
            Route::get($account.'/'.$orders.'/{order:number}', [AccountController::class, 'order'])->name('account.order');
            Route::post($account.'/'.$orders.'/{order:number}/'.$seg(['tr' => 'iptal', 'en' => 'cancel']),
                [AccountController::class, 'cancelOrder'])->name('account.order.cancel');

            $addresses = $seg(['tr' => 'adreslerim', 'en' => 'addresses']);

            Route::get($account.'/'.$addresses, [AccountController::class, 'addresses'])->name('account.addresses');
            Route::post($account.'/'.$addresses, [AccountController::class, 'storeAddress'])->name('account.addresses.store');
            Route::patch($account.'/'.$addresses.'/{address}', [AccountController::class, 'updateAddress'])->name('account.addresses.update');
            Route::delete($account.'/'.$addresses.'/{address}', [AccountController::class, 'destroyAddress'])->name('account.addresses.destroy');
        });
    });
};
