# Instalasi Proyek

Ikuti langkah-langkah berikut untuk menginstal dan menjalankan proyek ini secara lokal.

## Langkah 1: Install Dependensi
```
composer install
```

## Langkah 2: Install Package Laravel Sacntum
```
composer require laravel/sanctum
```

## Langkah 3: Salin File Konfigurasi `.env` dari `.env.example`

```
cp .env.example .env  
```

## Langkah 4: Generate Key Aplikasi  

```
php artisan key:generate  
```

## Langkah 5: Storage Link
```
php artisan storage:link
```

## Langkah 6: Konfigurasi `.env` untuk Terhubung ke DB

## Langkah 7: Migrasi Database  

```
php artisan migrate
```  

## Langkah 8: Seed Database

```
php artisan db:seed --class=ProvinsiSeeder
```

## Langkah 8: Jalankan Aplikasi secara Lokal  
Jalankan perintah berikut untuk menjalankan aplikasi.
```
php artisan serve
```
  
## Langkah 9: Testing
Lakukan testing dengan tools seperti **Postman** dan atau sebagainya.
