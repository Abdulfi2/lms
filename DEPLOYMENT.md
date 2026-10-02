# Deployment Checklist

Langkah-langkah yang wajib dijalankan saat deploy ke server produksi. Ini bukan
langkah kode — jalankan manual di server (atau masukkan ke script CI/CD).

## 1. Environment

- [ ] `.env` di server: `APP_ENV=production`, `APP_DEBUG=false`.
  Kalau `APP_DEBUG` lupa di-set `false`, stack trace error (termasuk query SQL,
  path server, dsb.) akan terekspos ke pengunjung.
- [ ] `APP_URL` diisi domain asli (bukan `localhost`).
- [ ] `APP_KEY` di-generate khusus untuk server ini (`php artisan key:generate`),
  jangan pakai key yang sama dengan environment lokal/staging.
- [ ] Kredensial DB, mail, dan (kalau dipakai) payment gateway diisi nilai asli
  produksi, bukan nilai contoh dari `.env.example`.

## 2. Install & build

```bash
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
```

## 3. Storage & file upload

- [ ] `php artisan storage:link` — wajib dijalankan sekali di server ini.
  Tanpa ini, upload thumbnail course/avatar/sertifikat akan tersimpan tapi
  tidak bisa diakses lewat URL publik (404).
- [ ] Pastikan folder `storage/` dan `bootstrap/cache/` writable oleh web server.

## 4. Queue worker

Job seperti `SendAssignmentNotificationJob` di-dispatch ke queue
(`QUEUE_CONNECTION=database`), bukan dieksekusi langsung. Tanpa worker yang
jalan, job-job ini akan menumpuk di tabel `jobs` dan tidak pernah terkirim.

- [ ] Jalankan `php artisan queue:work` sebagai proses background yang selalu
  hidup (pakai Supervisor atau systemd unit — jangan cuma jalankan manual di
  terminal, karena akan mati saat SSH terputus).
- [ ] Tambahkan cron untuk `php artisan schedule:run` tiap menit kalau ada
  scheduled task (cek `app/Console/Kernel.php`).

## 5. Performance (opsional tapi disarankan)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Ingat jalankan `php artisan config:clear` dulu kalau nanti mengubah `.env` di
server, karena `config:cache` membekukan nilai env saat itu juga.

## 6. Known follow-up (belum dikerjakan)

- **Payment gateway** masih manual (admin approve pembayaran secara manual di
  `Admin/EnrollmentController::markPaid`) — belum terintegrasi dengan
  Midtrans/Xendit/dsb. Lihat riwayat audit sebelumnya untuk detail.
- **`laravel/framework` masih punya 3 advisory keamanan** (signed URL path
  confusion & CRLF injection di email validation rule) yang baru benar-benar
  di-patch di Laravel 12.60+/13.10+ — proyek ini di Laravel 10, jadi tidak bisa
  ditutup lewat `composer update` biasa. Menutupnya butuh upgrade major version
  Laravel (proyek terpisah, bukan patch cepat). Sebagai mitigasi sementara:
  hindari menerima signed URL dari sumber tidak tepercaya, dan jangan
  mengandalkan validasi email bawaan (`email` rule) sebagai satu-satunya lapis
  anti-spoofing untuk alamat pengirim.
