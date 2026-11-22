# Matriks Skenario Pengujian Black-Box — iStore v3.1

- **Penyusun:** putri (Database & QA Engineer)
- **Branch Kerja:** `feature/database-seeders-automated-tests` (target PR ke `develop`, reviewer utama: `anmul`)
- **Acuan:** SRS v3.1, PRD v3.1, Architecture & Database Design v3.1, dan RTM Penugasan Putri
- **Aturan Pengujian:** Setiap fitur diuji pada skenario **Positif**, **Negatif**, dan **Otorisasi**; hasil `Actual` dan `Status` (`PASS` / `FAIL` / `PENDING`) diisi saat eksekusi aktual tanpa menutupi kegagalan.

---

## 1. Ringkasan Cakupan Modul Uji

| Kode Modul | Modul / Fitur | Cakupan Utama |
| :--- | :--- | :--- |
| `AUTH` | Autentikasi, Profil & RBAC | Registrasi Buyer, login/logout, rate limit `throttle:10,1`, redirect peran, edit profil Buyer. |
| `CAT` | Katalog Publik & Visibilitas | Beranda, katalog, search/filter/sort/pagination, visibilitas (`products.is_active = 1` & `categories.is_active = 1`), panel inspeksi unit `used`. |
| `CART` | Keranjang Belanja Persisten | 1 cart/Buyer, merge item, batas stok & maks `1` unit `used`, harga live, deteksi item stale & blokir checkout. |
| `CHK` | Atomic Two-Phase Checkout | Validasi server (`expected_total`, stok, unit `used`), Phase 1 transaksi DB & snapshot `order_items`, Phase 2 DOKU & retry aman. |
| `PAY` | Pembayaran DOKU & Auto-Refresh | Guard `DOKU_IS_PRODUCTION=false`, webhook HMAC-SHA256 & idempotensi `Request-Id`, status inquiry (`1x/15s`), auto-refresh (`5s`), restock idempoten via `stock_restored_at`, arsip `midtrans_sandbox` read-only. |
| `ORD` | Manajemen Pesanan | State machine `pending_payment` -> `paid` -> `processing` -> `shipped` -> `completed`, pembatalan sebelum `shipped` (sesi nonaktif), wajib kurir & resi saat `shipped`, konfirmasi terima oleh Buyer. |
| `SEL` | Panel Seller, Laporan & PDF | CRUD & toggle Kategori/Produk/Promosi, proteksi hapus relasi/FK `RESTRICT`, dashboard KPI & grafik Chart.js, catatan pembayaran read-only, laporan tanggal & ekspor PDF. |
| `DB` | Integritas Database & Seeder | 9 tabel domain / 114 kolom, constraint MySQL (`UNIQUE`, `CHECK`, `FK RESTRICT/CASCADE`), `migrate:fresh --seed` & rollback, seeder idempotent & tidak mengurangi stok berulang. |

---

## 2. Matriks Skenario Uji Black-Box

### A. Autentikasi, Profil & Otorisasi (`AUTH`)

| ID Skenario | Tipe | Fitur & Langkah Pengujian | Expected Result | Actual Result | Status |
| :--- | :---: | :--- | :--- | :--- | :---: |
| `BB-AUTH-01` | Positif | **Registrasi Buyer:** Akses `/register`, isi data valid, submit form. | Akun baru tercipta dengan `role = buyer`, password ter-hash, dan terautentikasi/diarahkan sesuai alur. | *(diisi saat test)* | `PENDING` |
| `BB-AUTH-02` | Positif | **Login Buyer & Seller:** Login akun `buyer` dan `seller` dengan kredensial valid. | Buyer diarahkan ke `/buyer/dashboard` dan Seller diarahkan ke `/seller/dashboard`. | *(diisi saat test)* | `PENDING` |
| `BB-AUTH-03` | Positif | **Logout Pengguna:** Pengguna terautentikasi menekan tombol/aksi logout. | Sesi berakhir dengan benar dan pengguna tidak dapat mengakses halaman privat tanpa login ulang. | *(diisi saat test)* | `PENDING` |
| `BB-AUTH-04` | Positif | **Update Profil Buyer:** Pada `/buyer/profile`, ubah Nama Lengkap, No. Telepon, dan Alamat Pengiriman Default lalu simpan. | Data profil tersimpan dan tampil kembali dengan benar. | *(diisi saat test)* | `PENDING` |
| `BB-AUTH-05` | Negatif | **Login Gagal Kredensial:** Kirim percobaan login dengan email tidak terdaftar atau password salah. | Login ditolak dengan pesan error validasi/autentikasi dan sesi tidak terbentuk. | *(diisi saat test)* | `PENDING` |
| `BB-AUTH-06` | Negatif | **Rate Limit Login/Register:** Kirim permintaan POST login/register berulang melebihi batas `throttle:10,1`. | Permintaan berlebih ditolak dengan respons rate limit (HTTP 429). | *(diisi saat test)* | `PENDING` |
| `BB-AUTH-07` | Negatif | **Perubahan Email/Input Tidak Valid di Profil:** Coba kirim perubahan `email` yang tidak diizinkan atau input profil tidak valid pada `/buyer/profile`. | Perubahan ditolak/diabaikan sesuai aturan profil Buyer dan data tetap konsisten. | *(diisi saat test)* | `PENDING` |
| `BB-AUTH-08` | Otorisasi | **Akses Halaman Privat oleh Guest:** Guest mengakses `/buyer/dashboard`, `/buyer/profile`, atau `/seller/dashboard`. | Guest diarahkan ke `/login`. | *(diisi saat test)* | `PENDING` |
| `BB-AUTH-09` | Otorisasi | **Akses Lintas Peran Buyer/Seller & Eskalasi Role:** Buyer mengakses rute khusus Seller (`/seller/dashboard`), Seller mengakses rute khusus Buyer (`/buyer/cart`), atau payload menyisipkan perubahan `role`. | Akses lintas peran ditolak (HTTP 403) dan eskalasi peran tidak diizinkan. | *(diisi saat test)* | `PENDING` |

### B. Katalog Publik & Visibilitas (`CAT`)

| ID Skenario | Tipe | Fitur & Langkah Pengujian | Expected Result | Actual Result | Status |
| :--- | :---: | :--- | :--- | :--- | :---: |
| `BB-CAT-01` | Positif | **Beranda & Promosi Aktif:** Buka `/` saat ada produk aktif, kategori aktif, dan promosi aktif (`start_date <= today <= end_date`). | Produk unggulan, kategori aktif, promosi dalam rentang tanggal berlaku, dan disclaimer retailer independen Apple di footer tampil. | *(diisi saat test)* | `PENDING` |
| `BB-CAT-02` | Positif | **Pencarian, Filter, Sort & Pagination Katalog:** Buka `/catalog`, uji pencarian kata kunci (`search`), filter kategori/kondisi (`new`/`used`)/grade (`A`/`B`/`C`)/harga, pengurutan (`sort`), dan perpindahan halaman (`pagination`). | Hasil katalog menyaring, mengurutkan, dan membagi halaman sesuai parameter yang dipilih. | *(diisi saat test)* | `PENDING` |
| `BB-CAT-03` | Positif | **Detail Produk & Panel Inspeksi Unit Second:** Buka halaman detail `/products/{id}` untuk produk `used` yang aktif. | Detail produk dan Panel Inspeksi Unit Second (grade, battery health, functional status, defect notes, included accessories, warranty info) tampil lengkap. | *(diisi saat test)* | `PENDING` |
| `BB-CAT-04` | Negatif | **Produk atau Kategori Nonaktif:** Nonaktifkan salah satu produk (`is_active = 0`) atau kategorinya (`is_active = 0`), lalu akses katalog & `/products/{id}`. | Produk tidak muncul di Beranda/Katalog dan akses langsung ke `/products/{id}` mengembalikan HTTP 404. | *(diisi saat test)* | `PENDING` |
| `BB-CAT-05` | Negatif | **Akses Produk Tidak Ditemukan:** Buka `/products/{id_tidak_ada}` atau filter dengan rentang tidak valid. | Mengembalikan HTTP 404 untuk ID tidak ada dan menangani parameter filter dengan aman tanpa error server. | *(diisi saat test)* | `PENDING` |
| `BB-CAT-06` | Otorisasi | **Akses Publik / Guest:** Akses `/`, `/catalog`, dan `/products/{id}` (aktif) tanpa login. | Dapat diakses penuh tanpa error otorisasi maupun kebocoran data privat. | *(diisi saat test)* | `PENDING` |

### C. Keranjang Belanja Persisten (`CART`)

| ID Skenario | Tipe | Fitur & Langkah Pengujian | Expected Result | Actual Result | Status |
| :--- | :---: | :--- | :--- | :--- | :---: |
| `BB-CART-01` | Positif | **Lazy Cart, Merge Kuantitas & Hapus Item:** Buyer menambahkan produk `new` (qty 1), menambahkan produk yang sama (qty 2), lalu menghapus semua item. | Maksimal 1 row `carts` per Buyer; qty tergabung (`3`); setelah semua item dihapus, row `carts` tetap dipertahankan. | *(diisi saat test)* | `PENDING` |
| `BB-CART-02` | Positif | **Harga Live & Deteksi Item Stale:** Tambahkan produk ke keranjang, lalu Seller mengubah harga produk atau menonaktifkan produk/kategori, kemudian buka `/buyer/cart`. | Subtotal memakai harga terkini produk; item tidak tersedia/kurang stok ditandai `stale` dan checkout diblokir hingga diselesaikan. | *(diisi saat test)* | `PENDING` |
| `BB-CART-03` | Negatif | **Kuantitas Tidak Valid atau Melebihi Stok:** Coba masukkan kuantitas `<= 0` atau melebihi stok tersedia ke keranjang. | Validasi menolak input kuantitas tidak valid atau melebihi stok. | *(diisi saat test)* | `PENDING` |
| `BB-CART-04` | Negatif | **Batas Maksimal 1 Unit `used`:** Coba masukkan kuantitas `> 1` untuk produk berkondisi `used`. | Validasi menolak kuantitas lebih dari `1` pada unit `used`. | *(diisi saat test)* | `PENDING` |
| `BB-CART-05` | Otorisasi | **Isolasi Keranjang Buyer:** Buyer A mengisi keranjang; Buyer B mengakses keranjang miliknya atau mencoba memodifikasi/menghapus item milik Buyer A. | Buyer B hanya melihat/mengelola keranjangnya sendiri; akses/ubah data milik Buyer lain ditolak. | *(diisi saat test)* | `PENDING` |

### D. Atomic Two-Phase Checkout & Pembayaran DOKU (`CHK` & `PAY`)

| ID Skenario | Tipe | Fitur & Langkah Pengujian | Expected Result | Actual Result | Status |
| :--- | :---: | :--- | :--- | :--- | :---: |
| `BB-CHK-01` | Positif | **Checkout Dua Fase Sukses:** Buyer submit checkout valid pada `/buyer/checkout` dengan gateway DOKU Sandbox aktif (`Http::fake()` pada test). | Phase 1 membuat `orders`, snapshot `order_items`, `payments` (`pending`), mengurangi stok, dan menghapus `cart_items`; Phase 2 mendapatkan data checkout DOKU. | *(diisi saat test)* | `PENDING` |
| `BB-CHK-02` | Negatif | **Kegagalan Gateway di Phase 2:** Simulasikan kegagalan/timeout panggilan eksternal DOKU pada Phase 2 saat checkout. | Order Phase 1 dan stok tetap konsisten untuk di-retry aman tanpa membuat order duplikat. | *(diisi saat test)* | `PENDING` |
| `BB-CHK-03` | Negatif | **Ketidaksesuaian `expected_total` / Item Stale:** Submit checkout saat `expected_total` tidak cocok atau keranjang masih memuat item `stale`/stok kurang. | Checkout ditolak oleh validasi server tanpa membuat pesanan baru. | *(diisi saat test)* | `PENDING` |
| `BB-PAY-01` | Positif | **Webhook Valid & Auto-Refresh:** Kirim notifikasi webhook DOKU valid (signature HMAC-SHA256 & nominal sesuai) dan pantau status order pada halaman detail Buyer/Seller. | `Request-Id` dicatat idempoten, status pembayaran/pesanan terverifikasi `paid`, dan halaman otomatis me-reload saat status berubah. | *(diisi saat test)* | `PENDING` |
| `BB-PAY-02` | Positif | **Restock Idempoten:** Batalkan pesanan yang memenuhi syarat atau proses kadaluarsa terminal dua kali/berulang. | Stok produk dikembalikan tepat satu kali (`stock_restored_at` terisi dan mencegah restock ganda). | *(diisi saat test)* | `PENDING` |
| `BB-PAY-03` | Negatif | **Signature/Nominal Invalid & Duplikasi `Request-Id`:** Kirim webhook DOKU dengan signature salah, nominal tidak cocok, atau `Request-Id` yang sudah diproses. | Webhook tidak sah ditolak tanpa mengubah DB; `Request-Id` duplikat diproses secara idempoten. | *(diisi saat test)* | `PENDING` |
| `BB-PAY-04` | Negatif | **Arsip `midtrans_sandbox` Read-Only:** Akses `pay`/`sync` atau kirim webhook pada transaksi historis `midtrans_sandbox`. | Transaksi `midtrans_sandbox` bersifat read-only (`pay`/`sync` ditolak HTTP 422, webhook lama HTTP 404). | *(diisi saat test)* | `PENDING` |
| `BB-PAY-05` | Otorisasi | **Ownership Detail Pesanan & Status Pembayaran:** Buyer B mencoba mengakses halaman detail atau endpoint status pembayaran milik Buyer A. | Akses ditolak (HTTP 403/404 sesuai Policy) sehingga Buyer tidak dapat melihat atau memengaruhi pesanan Buyer lain. | *(diisi saat test)* | `PENDING` |

### E. Manajemen Pesanan & Panel Seller (`ORD` & `SEL`)

| ID Skenario | Tipe | Fitur & Langkah Pengujian | Expected Result | Actual Result | Status |
| :--- | :---: | :--- | :--- | :--- | :---: |
| `BB-ORD-01` | Positif | **Alur Pesanan Lengkap:** Seller memproses order `paid` -> `processing`, menginput kurir & nomor resi lalu mengubah ke `shipped`, dan Buyer pemilik order mengonfirmasi terima -> `completed`. | Setiap transisi sah berhasil, data kurir & resi tersimpan, dan status akhir menjadi `completed`. | *(diisi saat test)* | `PENDING` |
| `BB-ORD-02` | Negatif | **Transisi `shipped` Tanpa Kurir/Resi:** Seller mencoba mengubah status pesanan menjadi `shipped` tanpa mengisi kurir atau nomor resi. | Transisi ke `shipped` ditolak oleh validasi. | *(diisi saat test)* | `PENDING` |
| `BB-ORD-03` | Negatif | **Pembatalan Tidak Sah:** Buyer mencoba membatalkan pesanan yang sudah `shipped` atau saat sesi pembayaran DOKU masih aktif. | Pembatalan ditolak sesuai aturan pembatalan pesanan. | *(diisi saat test)* | `PENDING` |
| `BB-SEL-01` | Positif | **CRUD & Toggle Kategori, Produk & Promosi:** Seller membuat, mengubah, mengaktifkan/menonaktifkan, dan menghapus (bila tidak berelasi) data Kategori, Produk (`new` & `used` lengkap inspeksi), serta Promosi. | Data tersimpan, terbarui, dan ter-toggle sesuai aturan masing-masing entitas. | *(diisi saat test)* | `PENDING` |
| `BB-SEL-02` | Positif | **Dashboard Seller, Laporan Tanggal & Ekspor PDF:** Buka dashboard Seller, filter laporan berdasarkan rentang tanggal, lalu unduh laporan PDF. | KPI dan grafik Chart.js tampil, data laporan sesuai filter tanggal, dan file PDF berhasil diunduh. | *(diisi saat test)* | `PENDING` |
| `BB-SEL-03` | Negatif | **Hapus Entitas Berelasi & Pembayaran Read-Only:** Seller mencoba menghapus kategori yang masih memiliki produk atau produk yang ada di `cart_items`/`order_items`, atau mencoba mengubah status pembayaran menjadi `paid` secara manual. | Penghapusan permanen entitas berelasi ditolak; halaman pembayaran tetap read-only dan perubahan status `paid` manual ditolak. | *(diisi saat test)* | `PENDING` |
| `BB-SEL-04` | Otorisasi | **Akses Panel/Aksi Seller oleh Buyer:** Akun Buyer mencoba mengakses halaman/aksi CRUD Seller atau laporan/PDF Seller. | Akses ditolak dengan HTTP 403. | *(diisi saat test)* | `PENDING` |
| `BB-SEL-05` | Otorisasi | **Konfirmasi Selesai oleh Buyer Lain / Seller:** Coba selesaikan pesanan (`shipped` -> `completed`) menggunakan akun Buyer lain atau akun Seller. | Ditolak karena penyelesaian pesanan hanya dapat dilakukan oleh Buyer pemilik pesanan. | *(diisi saat test)* | `PENDING` |

### F. Integritas Database & Seeder (`DB`)

| ID Skenario | Tipe | Fitur & Langkah Pengujian | Expected Result | Actual Result | Status |
| :--- | :---: | :--- | :--- | :--- | :---: |
| `BB-DB-01` | Positif | **Fresh Migrate, Rollback & Seeder Idempotent:** Jalankan `php artisan migrate:fresh --seed`, uji `migrate:rollback`, lalu jalankan ulang seeder. | Seluruh 9 tabel domain / 114 kolom terbentuk tanpa error, rollback berjalan, dan seeder aman dijalankan ulang tanpa duplikasi atau pengurangan stok berulang. | *(diisi saat test)* | `PENDING` |
| `BB-DB-02` | Negatif | **Pelanggaran Constraint & Relasi DB:** Uji duplikasi `email`/`slug`/`sku`, `price <= 0`, `stock < 0`, `battery_health > 100`, `end_date < start_date`, atau pelanggaran `RESTRICT`/`CASCADE`. | Database / validasi menolak data atau aksi yang melanggar constraint integritas. | *(diisi saat test)* | `PENDING` |
| `BB-DB-03` | Negatif | **Blokir Seeder Demo di Production:** Jalankan alur seeder data demo pada lingkungan yang diatur ke `production`. | Eksekusi seeder data demo diblokir/ditolak pada lingkungan `production`. | *(diisi saat test)* | `PENDING` |
| `BB-DB-04` | Otorisasi | **Pembatasan Akses Data Lintas Akun di Level Aplikasi:** Pastikan query/endpoint aplikasi selalu membatasi data privat (`cart`, `orders`, `payments`) sesuai pengguna terautentikasi dan perannya. | Pengguna tidak dapat mengakses atau memodifikasi data milik pengguna lain melalui manipulasi parameter ID. | *(diisi saat test)* | `PENDING` |