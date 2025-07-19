# Instalasi Proyek

Ikuti langkah-langkah berikut untuk menginstal dan menjalankan proyek ini secara lokal.

## Langkah 1: Install Dependensi
```
composer install
````

## Langkah 2: Salin File Konfigurasi `.env` dari `.env.example`

```
cp .env.example .env  
```

## Langkah 3: Generate Key Aplikasi  

```
php artisan key:generate  
```

## Langkah 4: Storage Link
```
php artisan storage:link
```

## Langkah 5: Buat Database MySQL  

## Langkah 6: Atur Konfigurasi `.env`  

Salin semua konten yang ada di file `.env.tmp` dan tempelkan ke dalam file `.env` untuk mengonfigurasi _environtment variable_ yang diperlukan.  

## Langkah 7: Migrasi Database  

```
php artisan migrate
```  

## Langkah 8: Seed Database

```
php artisan db:seed --class=UserSeeder
```

## Langkah 9: Jalankan Aplikasi secara Lokal  
Jalankan perintah berikut untuk menjalankan aplikasi.
```
php artisan serve
```
dan
```
npm run dev
```
