<?php

use App\Support\Locale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Yönlendirmeler aktif dili korur — İngilizce gezinen müşteri
        // Türkçe giriş sayfasına düşmesin.
        $middleware->redirectGuestsTo(fn () => Locale::route('login'));
        $middleware->redirectUsersTo(fn () => Locale::route('account'));

        // Yapım aşaması kapısı: oturum ve çerezler çözüldükten SONRA çalışmalı,
        // bu yüzden web grubunun sonuna eklenir. Panelin kendi ara katman
        // yığını var, oraya karışmaz.
        $middleware->web(append: \App\Http\Middleware\ConstructionGate::class);

        // Katalog kilidi auth'tan ÖNCE çalışmalı; yoksa /hesabim 404 yerine
        // (kapalı) giriş sayfasına yönlendiriyor.
        $middleware->prependToPriorityList(
            \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            \App\Http\Middleware\CatalogMode::class,
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
