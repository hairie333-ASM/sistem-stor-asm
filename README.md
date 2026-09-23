# Sistem Pengurusan Stor Kerajaan (TPS) — Akademi Sains Malaysia (ASM)

Aplikasi web pengurusan stor kerajaan gred pengeluaran berasaskan **Pekeliling Perbendaharaan Malaysia AM 6.1 hingga AM 6.10 (Tatacara Pengurusan Stor Kerajaan - TPS)**.

Sistem ini direka khas untuk **Akademi Sains Malaysia (ASM)** dengan sokongan penuh borang berkanun **KEW.PS-1 hingga KEW.PS-36**, pengasingan tugas (*Separation of Duties*), lejar transaksi tidak boleh diubah (*immutable ledger*), pemantauan paras stok automatik, formula Kadar Pusingan Stok (AM 6.6), dan modul carian pintar merentasi **981 item stok sebenar**.

---

## 🏛️ 9 Peranan Pengguna & Kredensial Ujian

Setiap peranan mempunyai paparan menu dan kuasa tindakan yang dihadkan mengikut pekeliling perbendaharaan:

| Bil | Peranan Pengguna | Emel Log Masuk | Kata Laluan | Tanggungjawab Utama |
|---|---|---|---|---|
| 1 | **Pentadbir Sistem** (`admin`) | `admin@asm.gov.my` | `password123` | Akses penuh konfigurasi sistem, log audit, penutupan tahunan |
| 2 | **Ketua Jabatan** (`ketua_jabatan`) | `ketua.jabatan@asm.gov.my` | `password123` | Kelulusan eksekutif: pelarasan (KEW.PS-16), pelupusan (KEW.PS-21), hapus kira (KEW.PS-35) |
| 3 | **Pegawai Stor** (`pegawai_stor`) | `pegawai.stor@asm.gov.my` | `password123` | Pengeluaran, kad petak (KEW.PS-4), pembungkusan (KEW.PS-9), lejar baki fizikal |
| 4 | **Pegawai Penerima** (`pegawai_penerima`) | `penerima@asm.gov.my` | `password123` | Pemeriksaan fizikal terimaan, jana KEW.PS-1 (BTB) & KEW.PS-2 (BPB) |
| 5 | **Pegawai Pelulus** (`pegawai_pelulus`) | `pelulus@asm.gov.my` | `password123` | Meluluskan pesanan stok individu KEW.PS-8 & antara stor KEW.PS-7 |
| 6 | **Pegawai Pemverifikasi** (`pemverifikasi`) | `pemverifikasi@asm.gov.my` | `password123` | Verifikasi tahunan 100% item fizikal, perakuan KEW.PS-13, laporan KEW.PS-14 |
| 7 | **Urus Setia Pelupusan** (`urus_setia_pelupusan`) | `pelupusan@asm.gov.my` | `password123` | Sediakan KEW.PS-19, koordinasi Lembaga Pemeriksa KEW.PS-20 & sijil KEW.PS-22 |
| 8 | **Urus Setia Kehilangan** (`urus_setia_kehilangan`) | `kehilangan@asm.gov.my` | `password123` | Lapor awal KEW.PS-32, jawatankuasa KEW.PS-33, syor KEW.PS-34, hapus kira KEW.PS-35 |
| 9 | **Pemohon / Staf** (`pemohon`) | `pemohon@asm.gov.my` | `password123` | Memohon pesanan stok KEW.PS-8, semakan katalog ASM & pemulangan stok |

---

## 🚀 Panduan Deployment: Supabase, GitHub & Render

### Bahagian 1: Supabase (Pangkalan Data PostgreSQL)
1. Buka [Supabase.com](https://supabase.com) dan log masuk/daftar akaun percuma.
2. Klik **"New Project"**, beri nama (cth: `asm-sistem-stor`), pilih rantau **Singapore (ap-southeast-1)**, dan tetapkan kata laluan pangkalan data anda.
3. Setelah projek siap, pergi ke **Project Settings -> Database**:
   - Salin **Connection String -> URI** (Pilih mod *Transaction* port 6543 atau *Session* port 5432):
     ```
     postgresql://postgres:[PASSWORD]@db.[PROJECT-REF].supabase.co:5432/postgres
     ```

### Bahagian 2: GitHub (Kawalan Kod Sumber)
1. Cipta repositori baharu di GitHub (contoh: `sistem-pengurusan-stor` atau `asm-sistem-stor`).
2. Di dalam direktori `sistem stor`, tolak kod ke repositori:
   ```bash
   cd "/Users/asm/Documents/Test Antigravity Sep 2026/sistem stor"
   git init
   git add .
   git commit -m "feat: Sistem Pengurusan Stor Kerajaan ASM (TPS AM 6.1 - AM 6.10)"
   git branch -M main
   git remote add origin https://github.com/USERNAME/REPO-NAME.git
   git push -u origin main
   ```

### Bahagian 3: Render (Pengehosan Web Awan)
1. Buka [Render.com](https://render.com) dan daftar/log masuk.
2. Klik **New +** -> **Blueprint**.
3. Sambungkan akaun GitHub anda dan pilih repositori `sistem-pengurusan-stor`.
4. Render akan membaca fail `render.yaml` secara automatik.
5. Masukkan nilai pembolehubah persekitaran (`Environment Variables`):
   - `DATABASE_URL`: Masukkan connection string Supabase anda dari Bahagian 1.
   - `DB_CONNECTION`: `pgsql`
   - `DB_SSLMODE`: `require`
6. Klik **Apply**.
7. Render akan membina kontena Docker (`Dockerfile`), menjalankan migrasi pangkalan data dan memasukkan kesemua **981 item stok sebenar** secara automatik.
8. Sistem anda akan aktif pada URL selamat HTTPS:
   ```
   https://asm-sistem-stor.onrender.com
   ```

---

## 💻 Panduan Menjalankan Secara Tempatan (Local Setup)

```bash
# 1. Masuk ke direktori projek
cd "/Users/asm/Documents/Test Antigravity Sep 2026/sistem stor"

# 2. Pasang kebergantungan PHP
composer install

# 3. Tetapkan fail persekitaran
cp .env.example .env
php artisan key:generate

# 4. Migrasi & Seed Pangkalan Data Tempatan (SQLite)
php artisan migrate --force
php artisan db:seed --force

# 5. Jalankan pelayan pembangunan tempatan
php artisan serve
```

Buka pelayar web di `http://127.0.0.1:8000` dan log masuk menggunakan mana-mana akaun ujian di atas.
