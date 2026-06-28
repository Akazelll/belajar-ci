# Toko Online — CodeIgniter 4

Aplikasi web **toko online** yang dibangun menggunakan [CodeIgniter 4](https://codeigniter.com/).
Proyek ini dibuat untuk mata kuliah **Pemrograman Web Lanjut** dan mencakup alur lengkap toko online: autentikasi pengguna, manajemen produk (CRUD), keranjang belanja, checkout dengan perhitungan ongkos kirim via **RajaOngkir**, pencatatan transaksi, riwayat pembelian, ekspor PDF, hingga **REST API** untuk data produk dan transaksi.

## Daftar Isi

- [Fitur](#fitur)
- [Teknologi](#teknologi)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
- [Konfigurasi Environment](#konfigurasi-environment)
- [Login Aplikasi](#login-aplikasi)
- [Struktur Database](#struktur-database)
- [Daftar Route](#daftar-route)
- [REST API](#rest-api)
- [Struktur Proyek](#struktur-proyek)

## Fitur

- **Autentikasi**
  - Login & logout berbasis session
  - Role pengguna (`admin` / `guest`)
  - Proteksi halaman menggunakan `auth` filter
- **Manajemen Produk (CRUD)**
  - Tambah, ubah, dan hapus produk
  - Upload & ganti foto produk (file disimpan di `public/img`)
  - Soft delete (data tidak benar-benar dihapus dari database)
  - Ekspor daftar produk ke **PDF** (Dompdf)
- **Keranjang Belanja**
  - Tambah produk ke keranjang
  - Ubah jumlah / hapus item
  - Kosongkan keranjang
  - Menggunakan modul [codeigniter4-cart-module](https://github.com/jason-napolitano/codeigniter4-cart-module)
- **Checkout & Transaksi**
  - Pencarian kota tujuan & perhitungan ongkos kirim via **RajaOngkir** (AJAX)
  - Penyimpanan transaksi + detail transaksi dalam satu *database transaction* (atomic)
  - Riwayat transaksi per pengguna
- **REST API** (autentikasi Bearer token)
  - Produk: CRUD penuh + pagination
  - Transaksi: list dengan filter tanggal + pagination
- **UI** responsif menggunakan template **NiceAdmin**

## Teknologi

| Komponen        | Versi / Pustaka                                   |
| --------------- | ------------------------------------------------- |
| PHP             | `^8.2`                                            |
| Framework       | `codeigniter4/framework ^4.7`                     |
| Generator PDF   | `dompdf/dompdf ^3.1`                              |
| Keranjang       | `jason-napolitano/codeigniter4-cart-module ^1.1`  |
| Data dummy      | `fakerphp/faker` (dev)                            |
| Pengujian       | `phpunit/phpunit ^10.5` (dev)                     |
| Ongkos kirim    | RajaOngkir API (eksternal)                        |

## Persyaratan Sistem

- PHP >= 8.2 beserta ekstensi `intl`, `mbstring`, `json`, dan `mysqlnd`
- Composer
- MySQL / MariaDB (mis. melalui XAMPP)
- Web server (development server bawaan `php spark serve` sudah cukup)
- API key [RajaOngkir](https://rajaongkir.com/) (untuk fitur ongkos kirim)

## Instalasi

1. **Clone repository**
   ```bash
   git clone <URL-repository>
   cd belajar-ci
   ```

2. **Install dependensi**
   ```bash
   composer install
   ```

3. **Siapkan file environment**

   Salin file `.env` (jika belum ada, buat dari template `env` bawaan CodeIgniter), lalu sesuaikan isinya — lihat [Konfigurasi Environment](#konfigurasi-environment).

4. **Buat database**
   - Jalankan Apache & MySQL (mis. dari XAMPP)
   - Buat database baru, mis. **`db_ci4`**, di phpMyAdmin

5. **Jalankan migrasi**
   ```bash
   php spark migrate
   ```

6. **Isi data awal (seeder)**
   ```bash
   php spark db:seed ProductSeeder
   php spark db:seed UserSeeder
   ```

7. **Jalankan server**
   ```bash
   php spark serve
   ```

8. **Akses aplikasi**
   Buka `http://localhost:8080` di browser.

## Konfigurasi Environment

Atur variabel berikut pada file `.env`:

```dotenv
app.baseURL = 'http://localhost:8080/'

# Database
database.default.hostname = localhost
database.default.database = db_ci4
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306

# RajaOngkir (fitur ongkos kirim)
RAJAONGKIR_API_KEY = your_rajaongkir_api_key
RAJAONGKIR_BASE_URL = https://rajaongkir.komerce.id/api/v1

# Token untuk REST API (dipakai pada header Authorization: Bearer <token>)
MY_API_KEY = your_secret_api_token
```

## Login Aplikasi

`UserSeeder` membuat 10 user acak menggunakan Faker. Seluruh user dibuat dengan **password yang sama**:

- **Password:** `1234567`
- **Username:** acak (cek tabel `user` di database untuk mengetahuinya)
- **Role:** acak antara `admin` dan `guest`

> Aturan validasi login: username minimal 6 karakter, password minimal 7 karakter dan berupa angka.

## Struktur Database

Schema dibuat melalui migrasi pada `app/Database/Migrations`. Seluruh tabel menggunakan **soft delete** (`deleted_at`) dan timestamp (`created_at`, `updated_at`).

**`product`**

| Kolom    | Tipe         | Keterangan        |
| -------- | ------------ | ----------------- |
| id       | INT (PK, AI) | Primary key       |
| nama     | VARCHAR(255) | Nama produk       |
| harga    | DOUBLE       | Harga             |
| jumlah   | INT          | Stok              |
| foto     | VARCHAR(255) | Nama file gambar  |

**`user`**

| Kolom    | Tipe         | Keterangan              |
| -------- | ------------ | ----------------------- |
| id       | INT (PK, AI) | Primary key             |
| username | VARCHAR(255) | Unik                    |
| email    | VARCHAR(255) | Unik                    |
| password | VARCHAR(255) | Hash (`password_hash`)  |
| role     | VARCHAR(50)  | `admin` / `guest`       |

**`transaction`**

| Kolom       | Tipe         | Keterangan                    |
| ----------- | ------------ | ----------------------------- |
| id          | INT (PK, AI) | Primary key                   |
| username    | VARCHAR(255) | Pemilik transaksi             |
| total_harga | DOUBLE       | Subtotal + ongkir             |
| alamat      | TEXT         | Alamat pengiriman             |
| ongkir      | DOUBLE       | Biaya ongkos kirim            |
| status      | INT(1)       | Status transaksi (0 = baru)   |

**`transaction_detail`**

| Kolom          | Tipe         | Keterangan                       |
| -------------- | ------------ | -------------------------------- |
| id             | INT (PK, AI) | Primary key                      |
| transaction_id | INT (FK)     | Relasi ke `transaction.id`       |
| product_id     | INT (FK)     | Relasi ke `product.id`           |
| jumlah         | INT          | Kuantitas                        |
| diskon         | DOUBLE       | Diskon item                      |
| subtotal_harga | DOUBLE       | jumlah × harga                   |

## Daftar Route

| Method | URI                       | Controller                       | Keterangan                       |
| ------ | ------------------------- | -------------------------------- | -------------------------------- |
| GET    | `/`                       | `Home::index`                    | Halaman utama (katalog)          |
| GET/POST | `login`                 | `AuthController::login`          | Form & proses login              |
| GET    | `logout`                  | `AuthController::logout`         | Logout                           |
| GET    | `produk`                  | `ProductController::index`       | Daftar produk (admin)            |
| POST   | `produk`                  | `ProductController::create`      | Tambah produk                    |
| POST   | `produk/edit/{id}`        | `ProductController::edit`        | Ubah produk                      |
| GET    | `produk/delete/{id}`      | `ProductController::delete`      | Hapus produk                     |
| GET    | `produk/download`         | `ProductController::download`    | Ekspor PDF                       |
| GET    | `keranjang`               | `TransactionController::index`   | Lihat keranjang                  |
| POST   | `keranjang`               | `TransactionController::cart_add`| Tambah ke keranjang              |
| POST   | `keranjang/edit`          | `TransactionController::cart_edit` | Ubah jumlah                    |
| GET    | `keranjang/delete/{rowid}`| `TransactionController::cart_delete` | Hapus item                  |
| GET    | `keranjang/clear`         | `TransactionController::cart_clear` | Kosongkan keranjang          |
| GET    | `checkout`                | `TransactionController::checkout`| Halaman checkout                 |
| POST   | `buy`                     | `TransactionController::buy`     | Proses pembelian                 |
| GET    | `history`                 | `TransactionController::history` | Riwayat transaksi                |
| GET    | `ajax/destinations`       | `TransactionController::destinations` | Cari kota (RajaOngkir)      |
| GET    | `ajax/costs`              | `TransactionController::costs`   | Hitung ongkir (RajaOngkir)       |

> Route bertanda kebutuhan login diproteksi oleh `auth` filter.

## REST API

Seluruh endpoint API memerlukan header autentikasi:

```
Authorization: Bearer <MY_API_KEY>
```

Jika token tidak valid, respons `401 Unauthorized` dikembalikan.

### Produk

| Method | Endpoint              | Keterangan                          |
| ------ | --------------------- | ----------------------------------- |
| GET    | `api/products`        | List produk (pagination)            |
| GET    | `api/products/{id}`   | Detail satu produk                  |
| POST   | `api/products`        | Tambah produk (body JSON)           |
| PUT    | `api/products/{id}`   | Ubah produk (body JSON)             |
| DELETE | `api/products/{id}`   | Hapus produk                        |

Query parameter list: `page` (default `1`), `per_page` (default `10`).

**Contoh — list produk:**
```bash
curl -H "Authorization: Bearer <MY_API_KEY>" \
  "http://localhost:8080/api/products?page=1&per_page=10"
```

**Contoh respons:**
```json
{
  "data": [
    { "id": 1, "nama": "Produk A", "harga": 15000, "jumlah": 10, "foto": "abc.jpg" }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "last_page": 3,
    "total_data": 25,
    "has_next": true,
    "has_prev": false
  }
}
```

**Contoh — tambah produk:**
```bash
curl -X POST -H "Authorization: Bearer <MY_API_KEY>" \
  -H "Content-Type: application/json" \
  -d '{"nama":"Produk Baru","harga":20000,"jumlah":5}' \
  "http://localhost:8080/api/products"
```

### Transaksi

| Method | Endpoint            | Keterangan                                   |
| ------ | ------------------- | -------------------------------------------- |
| GET    | `api/transactions`  | List transaksi + detail produk (pagination)  |

Query parameter: `start` & `end` (filter rentang tanggal pada `created_at`), `page`, `per_page`.

```bash
curl -H "Authorization: Bearer <MY_API_KEY>" \
  "http://localhost:8080/api/transactions?start=2026-01-01&end=2026-12-31&page=1"
```

Setiap transaksi pada respons menyertakan array `details` berisi item produk (nama, harga, foto, jumlah, subtotal).

## Struktur Proyek

```
app/
├── Config/
│   └── Routes.php                 # Definisi route
├── Controllers/
│   ├── Api/
│   │   ├── ProdukController.php    # REST API produk (CRUD + pagination)
│   │   └── TransaksiController.php # REST API transaksi (filter + pagination)
│   ├── AuthController.php          # Login & logout
│   ├── Home.php                   # Halaman utama
│   ├── ProductController.php       # CRUD produk + ekspor PDF
│   └── TransactionController.php   # Keranjang, checkout, pembelian, riwayat
├── Database/
│   ├── Migrations/                # Skema tabel
│   └── Seeds/                     # ProductSeeder, UserSeeder
├── Filters/
│   └── Auth.php                   # Proteksi route (auth filter)
├── Models/
│   ├── ProductModel.php
│   ├── UserModel.php
│   ├── TransactionModel.php
│   └── TransactionDetailModel.php
├── Services/
│   └── RajaOngkirService.php       # Integrasi API ongkos kirim
└── Views/
    ├── layout.php                 # Layout utama
    ├── v_home.php                 # Katalog produk
    ├── v_keranjang.php            # Keranjang belanja
    ├── v_checkout.php             # Checkout
    ├── v_history.php              # Riwayat transaksi
    ├── v_login.php                # Halaman login
    └── produk/                    # View CRUD produk + template PDF
public/
├── img/                          # Gambar produk & aset
└── NiceAdmin/                    # Template admin
```

## Lisensi

Proyek ini menggunakan lisensi [MIT](LICENSE).
