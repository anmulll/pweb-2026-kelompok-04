## Ringkasan Perubahan
- Jelaskan fitur atau perbaikan yang dikerjakan.

## Issue Terkait
- Closes #

## File Utama yang Diubah
- `path/to/file`

## Cara Menjalankan Pengujian
```bash
git diff --check
php artisan test
```

## Hasil Aktual Pengujian
- `php artisan test`: `... passed (... assertions)`
- `git diff --check`: bersih

## Screenshot (Jika Mengubah UI)
- *(Lampirkan screenshot atau tulis N/A)*

## Risiko Regresi & Catatan Migrasi
- Perubahan migration/env/route yang perlu diperhatikan anggota lain:
- Perintah yang perlu dijalankan setelah pull (`composer install`, `npm install`, `php artisan migrate`, dll.):

## Checklist Sebelum Merge
- [ ] Target PR ke `develop` (bukan langsung ke `main`)
- [ ] Tidak ada `.env`, `vendor/`, `node_modules/`, kredensial, atau database lokal yang ikut ter-commit
- [ ] Pengujian lulus tanpa merusak fitur sebelumnya
- [ ] Minimal 1 reviewer sudah ditetapkan
