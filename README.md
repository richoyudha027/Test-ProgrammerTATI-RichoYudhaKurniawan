# Instalasi Proyek

Ikuti langkah-langkah berikut untuk menginstal dan menjalankan proyek ini secara lokal.

## Langkah 1: Install Dependensi
```
composer install
```

## Langkah 2: Install Vite
```
npm install --save-dev vite
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

## Langkah 6: Atur Konfigurasi `.env` Untuk Terhubung ke DB  

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
