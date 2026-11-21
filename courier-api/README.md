# Courier API

REST API Laravel untuk master data kurir. Kolom: `name`, `phone`, `email`, `level` (1–5), dan `registered_at` (waktu terdaftar). `phone` dan `email` opsional dan unik.

## Menjalankan

Persyaratan: PHP 8.2+, Composer, dan SQLite atau MySQL.

```sh
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Tambahkan `routes/api.php` pada aplikasi Laravel 12 melalui konfigurasi routing API standar (`php artisan install:api` saat bootstrap project baru; jika file ini sudah tersedia, gunakan isi route di atas). Atur koneksi database di `.env`.

## Endpoint

- `GET /api/couriers?search=budi+agung&level=2,3&sort=registered_at&direction=desc&per_page=15` — pagination; default urut nama A–Z. Semua token pencarian harus cocok pada nama, urutan token bebas. Sort alternatif `registered_at`.
- `POST /api/couriers` — buat kurir.
- `GET /api/couriers/{id}` — detail.
- `PUT|PATCH /api/couriers/{id}` — update (PATCH menerima field parsial).
- `DELETE /api/couriers/{id}` — hapus, respons 204.

Contoh body create:

```json
{"name":"Budi Agung","phone":"0812345678","email":"budi@example.com","level":3}
```

Validasi mengharuskan nama dan level pada create; level integer 1–5, email valid, serta kontak unik. Field kontak boleh null. Untuk menjalankan feature test: `php artisan test`.
