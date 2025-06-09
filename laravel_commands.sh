# ===== INISIASI PROJECT LARAVEL =====

# 1. Install Laravel project baru
composer create-project laravel/laravel car-rental-system

# 2. Masuk ke direktori project
cd car-rental-system

# 3. Install dependency tambahan
composer require intervention/image
composer require spatie/laravel-permission
composer require laravel/sanctum
composer require spatie/laravel-backup

# ===== SETUP DATABASE & ENVIRONMENT =====

# 4. Copy file environment
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Setup database di .env file (edit manual)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=car_rental_db
# DB_USERNAME=root
# DB_PASSWORD=

# ===== MEMBUAT MODELS & MIGRATIONS =====

# 7. Buat model dan migration untuk Cars
php artisan make:model Car -mcr

# 8. Buat model dan migration untuk Bookings
php artisan make:model Booking -mcr

# 9. Buat model dan migration untuk Payments
php artisan make:model Payment -mcr

# 10. Buat model dan migration untuk Notifications
php artisan make:model Notification -mcr

# 11. Buat model dan migration untuk CarImages
php artisan make:model CarImage -mc

# ===== MEMBUAT CONTROLLERS =====

# 12. Controller untuk Admin
php artisan make:controller Admin/AdminController
php artisan make:controller Admin/CarController --resource
php artisan make:controller Admin/BookingController --resource
php artisan make:controller Admin/CustomerController
php artisan make:controller Admin/PaymentController
php artisan make:controller Admin/DashboardController

# 13. Controller untuk Customer
php artisan make:controller Customer/HomeController
php artisan make:controller Customer/CarController
php artisan make:controller Customer/BookingController --resource
php artisan make:controller Customer/ProfileController
php artisan make:controller Customer/PaymentController
php artisan make:controller Customer/NotificationController

# ===== MEMBUAT MIDDLEWARE =====

# 14. Middleware untuk Admin
php artisan make:middleware AdminMiddleware

# 15. Middleware untuk Customer
php artisan make:middleware CustomerMiddleware

# ===== MEMBUAT REQUESTS =====

# 16. Form Request untuk validasi
php artisan make:request CarRequest
php artisan make:request BookingRequest
php artisan make:request UserRequest
php artisan make:request PaymentRequest

# ===== MEMBUAT SERVICES =====

# 17. Buat direktori Services (manual)
mkdir app/Services

# 18. Service classes akan dibuat manual:
# - PaymentService.php
# - NotificationService.php  
# - BookingService.php

# ===== MEMBUAT SEEDERS =====

# 19. Seeder untuk data awal
php artisan make:seeder AdminSeeder
php artisan make:seeder CarSeeder
php artisan make:seeder UserSeeder

# ===== SETUP AUTHENTICATION =====

# 20. Install Laravel UI (untuk auth scaffolding)
composer require laravel/ui

# 21. Generate auth scaffolding
php artisan ui bootstrap --auth

# Atau jika ingin menggunakan Breeze (lebih modern)
# composer require laravel/breeze --dev
# php artisan breeze:install

# ===== SETUP STORAGE =====

# 22. Link storage folder
php artisan storage:link

# 23. Buat direktori untuk upload
mkdir public/storage/cars
mkdir public/storage/payment_proofs

# ===== MIGRATE DATABASE =====

# 24. Jalankan migration
php artisan migrate

# 25. Jalankan seeder (setelah dibuat)
php artisan db:seed

# ===== INSTALL FRONTEND DEPENDENCIES =====

# 26. Install NPM packages
npm install

# 27. Install Tailwind CSS (opsional)
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p

# Atau Bootstrap (jika menggunakan Laravel UI)
npm install bootstrap @popperjs/core

# 28. Install Alpine.js
npm install alpinejs

# 29. Install SweetAlert2
npm install sweetalert2

# ===== BUILD ASSETS =====

# 30. Compile assets
npm run dev

# Atau untuk production
# npm run build

# ===== SETUP PERMISSION =====

# 31. Publish Spatie Permission migration
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# 32. Migrate permission tables
php artisan migrate

# ===== CACHE OPTIMIZATION =====

# 33. Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 34. Optimize untuk production (nanti)
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache

# ===== JALANKAN SERVER =====

# 35. Start development server
php artisan serve

# Server akan berjalan di http://localhost:8000

# ===== COMMAND TAMBAHAN UNTUK DEVELOPMENT =====

# Membuat factory untuk testing
php artisan make:factory CarFactory
php artisan make:factory BookingFactory
php artisan make:factory PaymentFactory

# Membuat test files
php artisan make:test CarTest
php artisan make:test BookingTest
php artisan make:test PaymentTest

# Generate IDE helper (opsional, untuk autocomplete)
composer require --dev barryvdh/laravel-ide-helper
php artisan ide-helper:generate

# ===== STRUKTUR DIREKTORI YANG PERLU DIBUAT MANUAL =====

# Buat direktori berikut secara manual:
# app/Services/
# app/Traits/
# resources/views/admin/
# resources/views/customer/
# resources/views/components/
# resources/views/auth/
# public/storage/cars/
# public/storage/payment_proofs/

echo "Setup Laravel Car Rental System selesai!"
echo "Jangan lupa untuk:"
echo "1. Configure database di file .env"
echo "2. Edit migration files sesuai kebutuhan"
echo "3. Implement controller logic"
echo "4. Buat view files"
echo "5. Setup routes"