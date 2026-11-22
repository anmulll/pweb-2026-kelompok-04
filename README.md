# iStore — Toko Online Perangkat Apple (Laravel 12)

**iStore** adalah aplikasi web e-commerce single-seller berbasis Laravel 12 untuk retailer independen perangkat Apple (iPhone, MacBook, iPad, Apple Watch, AirPods, dan aksesori) yang menjual **produk Baru (New)** maupun **unit Second (Used) terinspeksi**.

> **Disclaimer:** iStore adalah retailer independen perangkat Apple, bukan Apple Store resmi, dan tidak berafiliasi dengan Apple Inc.

---

## Teknologi Utama

- **Backend:** PHP 8.3+, Laravel 12, Blade, Layered MVC + Domain Service Layer
- **Database:** MySQL 8.4 (InnoDB, `utf8mb4_unicode_ci`)
- **Frontend:** Tailwind CSS v4 (`@tailwindcss/vite`), Vite 6, Chart.js 4
- **PDF & Storage:** `barryvdh/laravel-dompdf` (^3.1), `league/flysystem-aws-s3-v3` (^3.35)
- **Payment Gateway:** DOKU Checkout Sandbox (Single Gateway, HMAC-SHA256)
- **Pengujian:** PHPUnit (`php artisan test`)

---

## Fitur Utama

- **Storefront Publik:** Beranda, katalog dengan pencarian/filter/sort/pagination, aturan visibilitas produk aktif (`SD-01`), dan panel inspeksi unit Second (Grade A/B/C, Kesehatan Baterai, Status Fungsi, Catatan Kekurangan, Kelengkapan, Garansi).
- **Autentikasi & RBAC:** Registrasi publik khusus `buyer`, login/logout terproteksi rate-limit (`throttle:10,1`), middleware `role:buyer` & `role:seller`, isolasi akses HTTP `403` lintas peran.
- **Keranjang Belanja & Two-Phase Checkout:** Persistent cart per Buyer, validasi stok/harga server-side, pemesanan atomik, dan integrasi pembayaran DOKU Checkout Sandbox + auto-refresh status.
- **Panel Seller & Analitik:** Dashboard KPI & grafik Chart.js, CRUD Kategori/Produk/Promosi, manajemen pesanan (proses, input resi/kirim, batal dengan restock idempoten), catatan pembayaran read-only, serta laporan penjualan & ekspor PDF.
- **Media Storage Scalable:** Mendukung penyimpanan lokal (`MEDIA_DISK=public`) untuk development dan Object Storage (`r2`/`s3`) untuk production.

---

## Prasyarat Lingkungan

| Komponen | Versi Disarankan |
| --- | --- |
| PHP | 8.3.x (`pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `curl`, `xml`, `bcmath`, `intl`) |
| Composer | 2.x |
| Database | MySQL 8.4.x |
| Node.js / npm | Node 20+ / npm 10+ |
| Git | 2.x |

---

## Prosedur Clone & Instalasi Lokal

1. **Clone repository kelompok:**
   ```bash
   git clone https://github.com/anmulll/pweb-2026-kelompok-04.git
   cd pweb-2026-kelompok-04
   git switch develop
   ```

2. **Install dependensi backend & frontend:**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Sesuaikan kredensial database lokal (`DB_DATABASE=istore_dev`, `DB_USERNAME`, `DB_PASSWORD`) pada file `.env`.

4. **Jalankan migrasi (dan seeder saat tersedia) serta storage link:**
   ```bash
   php artisan migrate
   php artisan storage:link
   ```

5. **Build aset dan jalankan server lokal:**
   ```bash
   npm run build
   php artisan serve
   ```
   Atau jalankan server development sekaligus:
   ```bash
   npm run dev
   ```

---

## Catatan DOKU Checkout Sandbox

- Sistem dikunci pada mode Sandbox (`PAYMENT_GATEWAY=doku_sandbox`, `DOKU_IS_PRODUCTION=false`).
- Isi `DOKU_CLIENT_ID` dan `DOKU_SECRET_KEY` hanya pada file `.env` lokal masing-masing.
- **Jangan pernah** meng-commit `.env`, `vendor/`, `node_modules/`, kredensial DOKU, atau file database lokal ke repository Git.

---

## Akun Demo

Akun demo (Buyer & Seller) disediakan melalui database seeder tim (akan diperbarui setelah seeder final di-merge ke `develop`). Registrasi publik melalui `/register` selalu membuat akun dengan peran `buyer`.

---

## Menjalankan Pengujian

```bash
php artisan test
```

Untuk memeriksa kebersihan diff sebelum commit:
```bash
git diff --check
```

---

## Kontrak Route & Field Auth (Koordinasi Frontend)

| Endpoint | Method | Middleware | Nama Route | Field Form |
| --- | --- | --- | --- | --- |
| `/register` | GET / POST | `guest` (+ `throttle:10,1` pada POST) | `register` | `name`, `email`, `phone` (opsional), `address` (opsional), `password`, `password_confirmation` |
| `/login` | GET / POST | `guest` (+ `throttle:10,1` pada POST) | `login` | `email`, `password`, `remember` (opsional) |
| `/logout` | POST | `auth` | `logout` | `@csrf` |
| `/buyer/dashboard` | GET | `auth`, `role:buyer` | `buyer.dashboard` | — |
| `/seller/dashboard` | GET | `auth`, `role:seller` | `seller.dashboard` | — |

---

## Alur Kerja Git & Kolaborasi

- `main`: Branch rilis terlindungi (wajib Pull Request dari `develop` + review).
- `develop`: Branch integrasi utama.
- `feature/<nama-fitur>`: Branch kerja masing-masing anggota, di-merge ke `develop` melalui Pull Request dengan minimal 1 reviewer.

---

## Checklist Deployment / Staging

1. Set `APP_ENV=production`, `APP_DEBUG=false`, dan `APP_URL=https://domain-anda.com`.
2. Konfigurasikan database dedicated MySQL 8.4 dan jalankan `php artisan migrate --force`.
3. Jalankan `php artisan storage:link` (atau set `MEDIA_DISK=r2`/`s3` jika memakai Object Storage).
4. Jalankan optimasi build & cache:
   ```bash
   npm run build
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## Anggota Tim & Peran

| Anggota | Peran |
| --- | --- |
| `anmul` | Project Manager + Documentation & DevOps (Bootstrap, Auth & RBAC) |
| `putri` | Reviewer Utama & QA / Integrasi |
| `gizalda` | Frontend UI (Auth Views, Storefront & Blade Components) |
| `adel` | Database & Backend |
| `caca` | Backend & Routing |
