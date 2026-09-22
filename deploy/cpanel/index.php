<?php

/*
 * public_html/index.php — cPanel yerleşimi için giriş dosyası.
 *
 * Alan adının kök dizini değiştirilemediği için Laravel'in tamamı
 * public_html/necipoglu_app/ altında duruyor; web kökünde yalnızca public/
 * içeriği ve bu dosya var.
 *
 * necipoglu_app/.htaccess klasörü web'e tamamen kapatır (deploy her seferinde
 * yeniden koyar). O dosya olmazsa .env dışarıdan okunabilir — kurulumdan
 * sonra mutlaka doğrulayın, bkz. DEPLOY.md.
 *
 * Laravel'in kendi public/index.php dosyası olduğu gibi duruyor; yerel
 * geliştirme onu kullanmaya devam ediyor.
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$app_base = __DIR__.'/necipoglu_app';

if (file_exists($maintenance = $app_base.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $app_base.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $app_base.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
