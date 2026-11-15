
* **Nama:** Aminatus Zarodiyah Riberna
* **NIM:** 202410370110160
* **Program Studi:** Informatika


## Implementasi Konsep PHP OOP

* **Namespace & Autoloading (`autoload.php`):** Menggunakan `spl_autoload_register` dengan standar PSR-4 (`App\Contracts`, `App\Models`, `App\Services`).
* **Interface & Polymorphism (`src/Contracts/RentableInterface.php`):** Perhitungan tarif dasar berbeda antara `Ebook` (tarif harian normal) dan `AudioBook` (tarif harian + biaya lisensi streaming audio Rp 2.000), serta penerapan diskon otomatis 20% khusus `PremiumMember`.
* **Inheritance (`src/Models/`):**
  * `Ebook` dan `AudioBook` mewarisi abstract class `MediaItem`.
  * `RegularMember` dan `PremiumMember` mewarisi abstract class `User`.
* **Encapsulation:** Seluruh properti model menggunakan visibilitas `private` dan `protected`, dengan perubahan stok lisensi yang dikontrol melalui method `decrementStock()`, `incrementStock()`, dan `restock()`.
* **Penyimpanan Data (`data/`):** Persistensi data katalog, member, dan transaksi menggunakan format JSON (`media.json`, `users.json`, `transactions.json`).

---

## Cara Menjalankan Aplikasi

1. Pastikan **PHP 8.x** sudah terpasang di perangkat.
2. Buka terminal pada direktori utama proyek ini, lalu jalankan perintah:
   ```bash
   php -S localhost:8000
