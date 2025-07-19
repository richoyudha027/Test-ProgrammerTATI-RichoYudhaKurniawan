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

## Langkah 5: Jalankan Aplikasi secara Lokal  
Jalankan perintah berikut untuk menjalankan aplikasi.
```
php artisan serve
```
