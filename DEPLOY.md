# Canlıya alma — cPanel + Git

Genel tarif ve tüm tuzakların hikâyesi: `F:\Yazılımlar\CPANEL-GIT-DEPLOY.md`
(kaynak: Ay Parçası). Bu belge o tarifin Necipoğlu'na uyarlanmış hâlidir.

Yerleşim — alan adının kök dizini değiştirilemediği varsayımıyla:

```
public_html/
├── index.php          ← deploy/cpanel/index.php
├── .htaccess          ← public/.htaccess
├── build/ brand/ market/ css/ js/ fonts/ favicon… robots.txt
├── storage/           ← GERÇEK klasör; panelden yüklenen görseller buraya
└── necipoglu_app/     ← Laravel'in tamamı + .env
    └── .htaccess      ← "Require all denied" (deploy her seferinde koyar)
```

---

## Canlı kurulum (2026-09-22) — gerçek değerler

| | |
|---|---|
| cPanel hesabı | `neci2913` (Turhost, CloudLinux) |
| PHP | Select PHP Version → **8.4** (8.3'ten alındı; vendor 8.4.1 ister) |
| Depo (sunucu) | `/home/neci2913/repositories/necipoglu` ← github.com/ahmeterhancy-cpu/necipoglu |
| Uygulama | `public_html/necipoglu_app` (+ `.env`, `.htaccess` → 403 doğrulandı) |
| Veritabanı | `neci2913_site`, kullanıcı `neci2913_necipog` (cPanel 16 karakterde keser) |
| Eski site | `public_html/index.html.eski` (orijinali cPanel çöp kutusunda) |

İlk kurulumda çıkan sorunlar (hepsi çözüldü, kodda kalıcı):

1. **MySQL varsayılan motoru MyISAM** → `users.email` benzersiz anahtarı "max key
   length is 1000 bytes". `config/database.php` artık `engine => InnoDB`.
   Yarım kalan ilk veritabanı (`neci2913_necipog`) boş; silinebilir.
2. **Önbellekli config** → `.env`'de veritabanı adı değişti ama göçler eskisine
   bağlandı. `.cpanel.yml` artık göçlerden önce `config:clear` çalıştırır.
3. **APP_KEY** elle yapıştırılmaz: `.env`'de yoksa deploy üretir.

---

## 0 · Önce öğrenilecekler

| Soru | Neden |
|---|---|
| cPanel kullanıcı adı | `.cpanel.yml` ve `.env`'deki `KULLANICI` |
| PHP sürümü (MultiPHP **ve** PHP Selector) | `composer.json` → `config.platform.php` |
| Sunucuda `composer` var mı? | Yoksa `vendor/` depoya girer (bkz. 3) |
| SSH / Terminal var mı? | Yoksa her şey deploy görevlerinden yürür (bu belge öyle varsayar) |

---

## 1 · Depo

- GitHub'da depo, dal `main`. cPanel klon adresinde parola kabul etmediği için
  depo pratikte **public** olur.
- `.env` hiç commit'lenmedi. Yönetici parolası artık kodda yok (`.env` →
  `ADMIN_PASSWORD`). **Eski git geçmişinde yerel parola geçiyor** — depo
  geçmişsiz (tek commit) yayınlanır; canlı yönetici parolası zaten farklıdır.

## 2 · cPanel'de klonla

Git Version Control → Create → Clone URL `https://github.com/…/necipoglu.git`,
Repository Path `repositories/necipoglu`. Hedef klasör boş olmalı.

## 3 · vendor (sunucuda composer yoksa)

```bash
composer config platform.php 8.x.y        # sunucunun sürümü
composer update --no-dev --optimize-autoloader
git add -f vendor
```

Yerelde geliştirmeye dönmek için `composer install`. Değişen 6 dosya için
bkz. genel tarif §5 (`git update-index --skip-worktree …`).

## 4 · Veritabanı ve .env (tek seferlik)

1. cPanel → MySQL Veritabanları: veritabanı + kullanıcı, **tüm yetkiler**.
2. Dosya Yöneticisi → Show Hidden Files → `public_html/necipoglu_app/.env`
   oluştur, `.env.production.example`'dan doldur:
   - `APP_KEY` → yerelde `php artisan key:generate --show`
   - `DB_*` (`DB_HOST=localhost`)
   - `FILESYSTEM_PUBLIC_ROOT=/home/KULLANICI/public_html/storage`
   - `ADMIN_EMAIL`, `ADMIN_PASSWORD` (en az 10 karakter)
   - `CONSTRUCTION_PIN` (isteğe bağlı)

İlk deploy `.env`'den ÖNCE yapılırsa `migrate` atlanır; `.env`'i yazıp
deploy'u tekrarlamak yeter.

## 5 · Deploy ne yapar (`.cpanel.yml`)

1. Uygulamayı `necipoglu_app/`'e kopyalar, klasörü web'e kapatır
2. `public/` içeriğini web köküne koyar
3. `deploy/ilk-medya/media` yer tutucu görsellerini `public_html/storage/`'a
   koyar — **var olan dosyanın üstüne yazmaz**
4. `migrate --force`
5. `site:kur` — **yalnızca veritabanı boşken**: şubeler, kategoriler, site
   ayarları, 30 marka ve tanıtım yazıları; **yapım aşaması sayfası AÇIK** gelir.
   Örnek ürün ve referanslar canlıya girmez.
6. `admin:olustur` — **hiç yönetici yokken** `.env`'deki ADMIN_* ile ilk hesabı açar;
   diğer yöneticiler panelden eklenir (Kurumsal > Yöneticiler)
7. `optimize`, `filament:optimize`

`migrate:fresh` ve `db:seed` deploy'da **yok ve olmamalı**.

## 6 · İlk kurulumdan sonra kontrol

- [ ] `https://ALANADI/necipoglu_app/.env` → **403**
- [ ] Site → yapım aşaması sayfası (TR ve `/en`)
- [ ] `/admin` → giriş yapılıyor; sonra `.env`'den `ADMIN_PASSWORD` silinir
- [ ] Panel → Site Ayarları → Yapım aşaması → PIN verilir
- [ ] PIN girilen cihazda site açılıyor, görseller geliyor
- [ ] Panelden bir görsel yükle → sitede görünüyor (`FILESYSTEM_PUBLIC_ROOT`)
- [ ] `/sitemap.xml` XML döndürüyor, `/robots.txt` gerçek alan adını gösteriyor
- [ ] `APP_DEBUG=false` — hata sayfası ayrıntı sızdırmıyor
- [ ] `http://` → `https://` yönlendirmesi çalışıyor

## 7 · Yayın döngüsü

1. `npm run build` → `public/build` commit (sunucuda Node yok)
2. `git push`
3. cPanel → Git Version Control → Manage → **Update from Remote** →
   **Deploy HEAD Commit** (iki ayrı düğme; yalnız ikincisi eski kodu kurar)
4. "Last Deployed" son commit mi? `~/deploy-son.log` sonunda
   `=== DEPLOY BITTI ===` var mı?

Siteyi yayına açmak: Panel → Site Ayarları → Yapım aşaması → anahtarı kapat.

## Sorun tablosu

Genel tarif §9. Bu projeye özgü:

| Belirti | Sebep |
|---|---|
| Yapım aşaması açıkken panelde kaydet çalışmıyor | Livewire rastgele önekli — `ConstructionGate::OPEN` içinde `livewire*` olmalı (öyle) |
| Kategori / ana sayfa görseli yok | `deploy/ilk-medya` kopyalanmadı ya da `FILESYSTEM_PUBLIC_ROOT` yanlış |
| Paneldeki metinler kurulum hâline döndü | Birisi `db:seed` çalıştırdı — deploy'a EKLEMEYİN |
