# Rancangan Website Rental Mobil
## Laravel & MySQL

---

## 1. OVERVIEW SISTEM

### Fitur Utama:
- **Customer Panel**: Registrasi, login, browsing mobil, booking, pembayaran, riwayat rental
- **Admin Panel**: Manajemen mobil, booking, customer, laporan, dashboard
- **Payment System**: Transfer bank dengan upload bukti pembayaran
- **Notification System**: Panel notifikasi di interface user (in-app notifications)

---

## 2. DATABASE DESIGN

### ERD (Entity Relationship Diagram)

```
Users (customers & admin)
├── id (PK)
├── name
├── email
├── phone
├── address
├── identity_number (KTP/SIM)
├── role (customer/admin)
├── email_verified_at
├── password
└── timestamps

Cars
├── id (PK)
├── brand
├── model
├── year
├── license_plate
├── color
├── transmission (manual/automatic)
├── fuel_type
├── seats
├── price_per_day
├── status (available/rented/maintenance)
├── description
├── image_path
└── timestamps

Bookings
├── id (PK)
├── user_id (FK)
├── car_id (FK)
├── start_date
├── end_date
├── total_days
├── total_price
├── status (pending/confirmed/ongoing/completed/cancelled)
├── pickup_location
├── return_location
├── notes
└── timestamps

Payments
├── id (PK)
├── booking_id (FK)
├── amount
├── payment_method (bca/mandiri/bni/bri)
├── payment_status (pending/verified/rejected)
├── payment_proof (upload bukti transfer)
├── payment_date
├── verified_at
├── verified_by (admin_id)
├── notes
└── timestamps

Notifications
├── id (PK)
├── user_id (FK)
├── title
├── message
├── type (booking/payment/general)
├── is_read
├── created_at
└── updated_at

Car_Images
├── id (PK)
├── car_id (FK)
├── image_path
├── is_primary
└── timestamps
```

---

## 3. STRUKTUR FILE LARAVEL

```
car-rental-system/
├── app/
│   ├── Console/
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── AdminController.php
│   │   │   │   ├── CarController.php
│   │   │   │   ├── BookingController.php
│   │   │   │   ├── CustomerController.php
│   │   │   │   ├── PaymentController.php
│   │   │   │   └── DashboardController.php
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   ├── RegisterController.php
│   │   │   │   └── ForgotPasswordController.php
│   │   │   ├── Customer/
│   │   │   │   ├── HomeController.php
│   │   │   │   ├── CarController.php
│   │   │   │   ├── BookingController.php
│   │   │   │   ├── ProfileController.php
│   │   │   │   ├── PaymentController.php
│   │   │   │   └── NotificationController.php
│   │   │   └── Controller.php
│   │   ├── Middleware/
│   │   │   ├── AdminMiddleware.php
│   │   │   ├── CustomerMiddleware.php
│   │   │   └── Authenticate.php
│   │   └── Requests/
│   │       ├── CarRequest.php
│   │       ├── BookingRequest.php
│   │       └── UserRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Car.php
│   │   ├── Booking.php
│   │   ├── Payment.php
│   │   ├── Notification.php
│   │   └── CarImage.php
│   ├── Providers/
│   ├── Services/
│   │   ├── PaymentService.php
│   │   ├── NotificationService.php
│   │   └── BookingService.php
│   └── Traits/
│       └── ImageUploadTrait.php
├── bootstrap/
├── config/
│   ├── app.php
│   ├── database.php
│   ├── mail.php
│   └── payment.php
├── database/
│   ├── factories/
│   │   ├── UserFactory.php
│   │   ├── CarFactory.php
│   │   └── BookingFactory.php
│   ├── migrations/
│   │   ├── 2024_01_01_000000_create_users_table.php
│   │   ├── 2024_01_02_000000_create_cars_table.php
│   │   ├── 2024_01_03_000000_create_bookings_table.php
│   │   ├── 2024_01_04_000000_create_payments_table.php
│   │   ├── 2024_01_05_000000_create_car_images_table.php
│   │   └── 2024_01_06_000000_create_notifications_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── UserSeeder.php
│       ├── CarSeeder.php
│       └── AdminSeeder.php
├── public/
│   ├── css/
│   ├── js/
│   ├── images/
│   │   └── cars/
│   └── storage/
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   └── app.js
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php
│       │   ├── admin.blade.php
│       │   └── customer.blade.php
│       ├── auth/
│       │   ├── login.blade.php
│       │   ├── register.blade.php
│       │   └── forgot-password.blade.php
│       ├── admin/
│       │   ├── dashboard.blade.php
│       │   ├── cars/
│       │   │   ├── index.blade.php
│       │   │   ├── create.blade.php
│       │   │   ├── edit.blade.php
│       │   │   └── show.blade.php
│       │   ├── bookings/
│       │   │   ├── index.blade.php
│       │   │   └── show.blade.php
│       │   ├── payments/
│       │   │   ├── index.blade.php
│       │   │   └── verify.blade.php
│       │   └── customers/
│       │       └── index.blade.php
│       ├── customer/
│       │   ├── home.blade.php
│       │   ├── cars/
│       │   │   ├── index.blade.php
│       │   │   └── show.blade.php
│       │   ├── bookings/
│       │   │   ├── create.blade.php
│       │   │   ├── index.blade.php
│       │   │   └── show.blade.php
│       │   ├── profile/
│       │   │   └── edit.blade.php
│       │   └── payments/
│       │       └── checkout.blade.php
│       └── components/
│           ├── navbar.blade.php
│           ├── footer.blade.php
│           └── car-card.blade.php
├── routes/
│   ├── web.php
│   ├── api.php
│   └── admin.php
├── storage/
├── tests/
├── vendor/
├── .env
├── .env.example
├── artisan
├── composer.json
├── package.json
└── README.md
```

---

## 4. RUTE (ROUTES)

### Web Routes (routes/web.php)
```php
// Home & Authentication
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
Route::get('/cars/{car}', [CarController::class, 'show'])->name('cars.show');

// Auth Routes
Auth::routes();

// Customer Routes (Protected)
Route::middleware(['auth', 'customer'])->group(function () {
    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
        Route::resource('/bookings', BookingController::class);
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/payments/{booking}', [PaymentController::class, 'process'])->name('payments.process');
        Route::post('/payments/{booking}/upload', [PaymentController::class, 'uploadProof'])->name('payments.upload');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    });
});

// Admin Routes (Protected)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::resource('/cars', CarController::class);
        Route::resource('/bookings', BookingController::class);
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
        Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    });
});
```

---

## 5. MODELS & RELATIONSHIPS

### User Model
```php
class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'identity_number', 'role', 'password'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }
}
```

### Car Model
```php
class Car extends Model
{
    protected $fillable = [
        'brand', 'model', 'year', 'license_plate', 'color', 
        'transmission', 'fuel_type', 'seats', 'price_per_day', 
        'status', 'description', 'image_path'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function images()
    {
        return $this->hasMany(CarImage::class);
    }

    public function isAvailable()
    {
        return $this->status === 'available';
    }
}
```

### Booking Model
```php
class Booking extends Model
{
    protected $fillable = [
        'user_id', 'car_id', 'start_date', 'end_date', 
        'total_days', 'total_price', 'status', 
        'pickup_location', 'return_location', 'notes'
    ];

    protected $dates = ['start_date', 'end_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}

### Payment Model
```php
class Payment extends Model
{
    protected $fillable = [
        'booking_id', 'amount', 'payment_method', 'payment_status',
        'payment_proof', 'payment_date', 'verified_at', 'verified_by', 'notes'
    ];

    protected $dates = ['payment_date', 'verified_at'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isPending()
    {
        return $this->payment_status === 'pending';
    }

    public function isVerified()
    {
        return $this->payment_status === 'verified';
    }
}

### Notification Model
```php
class Notification extends Model
{
    protected $fillable = [
        'user_id', 'title', 'message', 'type', 'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
}
```

---

## 6. MIGRATION FILES

### Cars Migration
```php
Schema::create('cars', function (Blueprint $table) {
    $table->id();
    $table->string('brand');
    $table->string('model');
    $table->year('year');
    $table->string('license_plate')->unique();
    $table->string('color');
    $table->enum('transmission', ['manual', 'automatic']);
    $table->string('fuel_type');
    $table->integer('seats');
    $table->decimal('price_per_day', 10, 2);
    $table->enum('status', ['available', 'rented', 'maintenance'])->default('available');
    $table->text('description')->nullable();
    $table->string('image_path')->nullable();
    $table->timestamps();
});
```

### Payments Migration
```php
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('booking_id')->constrained()->onDelete('cascade');
    $table->decimal('amount', 10, 2);
    $table->enum('payment_method', ['bca', 'mandiri', 'bni', 'bri']);
    $table->enum('payment_status', ['pending', 'verified', 'rejected'])->default('pending');
    $table->string('payment_proof')->nullable(); // path to uploaded proof
    $table->datetime('payment_date')->nullable();
    $table->timestamp('verified_at')->nullable();
    $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

### Notifications Migration
```php
Schema::create('notifications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('title');
    $table->text('message');
    $table->enum('type', ['booking', 'payment', 'general'])->default('general');
    $table->boolean('is_read')->default(false);
    $table->timestamp('created_at')->useCurrent();
    $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
});
```

---

## 7. CONTROLLERS UTAMA

### Admin CarController
```php
class CarController extends Controller
{
    public function index()
    {
        $cars = Car::with('images')->paginate(10);
        return view('admin.cars.index', compact('cars'));
    }

    public function store(CarRequest $request)
    {
        $car = Car::create($request->validated());
        
        if ($request->hasFile('image')) {
            $car->image_path = $this->uploadImage($request->file('image'));
            $car->save();
        }

        return redirect()->route('admin.cars.index')->with('success', 'Mobil berhasil ditambahkan');
    }
}
```

### Customer PaymentController
```php
class PaymentController extends Controller
{
    public function process(Request $request, Booking $booking)
    {
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'payment_method' => $request->payment_method,
            'payment_date' => now(),
        ]);

        return view('customer.payments.transfer-instructions', compact('payment', 'booking'));
    }

    public function uploadProof(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $payment = $booking->payment;
        
        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payment_proofs', 'public');
            $payment->update(['payment_proof' => $path]);
        }

        // Create notification for admin
        Notification::create([
            'user_id' => 1, // admin user
            'title' => 'Bukti Pembayaran Baru',
            'message' => "Bukti pembayaran untuk booking #{$booking->id} telah diupload",
            'type' => 'payment'
        ]);

        return redirect()->route('customer.bookings.show', $booking->id)
                        ->with('success', 'Bukti pembayaran berhasil diupload');
    }
}
```

### Admin PaymentController
```php
class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['booking.user', 'booking.car'])
                          ->where('payment_status', 'pending')
                          ->latest()
                          ->paginate(10);
        
        return view('admin.payments.index', compact('payments'));
    }

    public function verify(Payment $payment)
    {
        $payment->update([
            'payment_status' => 'verified',
            'verified_at' => now(),
            'verified_by' => auth()->id()
        ]);

        // Update booking status
        $payment->booking->update(['status' => 'confirmed']);

        // Create notification for customer
        Notification::create([
            'user_id' => $payment->booking->user_id,
            'title' => 'Pembayaran Diverifikasi',
            'message' => "Pembayaran untuk booking #{$payment->booking->id} telah diverifikasi",
            'type' => 'payment'
        ]);

        return back()->with('success', 'Pembayaran berhasil diverifikasi');
    }

    public function reject(Request $request, Payment $payment)
    {
        $payment->update([
            'payment_status' => 'rejected',
            'notes' => $request->notes
        ]);

        // Create notification for customer
        Notification::create([
            'user_id' => $payment->booking->user_id,
            'title' => 'Pembayaran Ditolak',
            'message' => "Pembayaran untuk booking #{$payment->booking->id} ditolak. Alasan: {$request->notes}",
            'type' => 'payment'
        ]);

        return back()->with('success', 'Pembayaran ditolak');
    }
}
```
```

---

## 8. MIDDLEWARE

### AdminMiddleware
```php
class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }

        return $next($request);
    }
}
```

---

## 9. SERVICES

### PaymentService
```php
class PaymentService
{
    public function getBankAccounts()
    {
        return [
            'bca' => [
                'name' => 'Bank BCA',
                'account_number' => '1234567890',
                'account_name' => 'CV Rental Mobil'
            ],
            'mandiri' => [
                'name' => 'Bank Mandiri',
                'account_number' => '0987654321',
                'account_name' => 'CV Rental Mobil'
            ],
            'bni' => [
                'name' => 'Bank BNI',
                'account_number' => '1122334455',
                'account_name' => 'CV Rental Mobil'
            ],
            'bri' => [
                'name' => 'Bank BRI',
                'account_number' => '5566778899',
                'account_name' => 'CV Rental Mobil'
            ]
        ];
    }

    public function getPaymentInstructions($method)
    {
        $accounts = $this->getBankAccounts();
        return $accounts[$method] ?? null;
    }
}
```

### NotificationService
```php
class NotificationService
{
    public function createNotification($userId, $title, $message, $type = 'general')
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type
        ]);
    }

    public function notifyPaymentUploaded($booking)
    {
        // Notify all admins
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            $this->createNotification(
                $admin->id,
                'Bukti Pembayaran Baru',
                "Bukti pembayaran untuk booking #{$booking->id} telah diupload",
                'payment'
            );
        }
    }

    public function notifyPaymentVerified($booking)
    {
        $this->createNotification(
            $booking->user_id,
            'Pembayaran Diverifikasi',
            "Pembayaran untuk booking #{$booking->id} telah diverifikasi. Mobil siap digunakan!",
            'payment'
        );
    }
}
```

---

## 10. FRONTEND FRAMEWORK

### CSS Framework: **Tailwind CSS** atau **Bootstrap 5**
### JavaScript: **Alpine.js** atau **Vue.js**
### Icons: **Heroicons** atau **Font Awesome**

---

## 11. PACKAGE TAMBAHAN

### Composer Packages:
```json
{
    "intervention/image": "^2.7",
    "spatie/laravel-permission": "^5.0",
    "laravel/sanctum": "^3.0",
    "spatie/laravel-backup": "^8.0"
}
```

### NPM Packages:
```json
{
    "@tailwindcss/forms": "^0.5.0",
    "alpinejs": "^3.0.0",
    "axios": "^1.0.0",
    "sweetalert2": "^11.0.0"
}
```

---

## 12. DEPLOYMENT CHECKLIST

1. **Environment Setup**
   - Configure `.env` file
   - Set APP_KEY, DB credentials, Mail settings

2. **Database Setup**
   - Run migrations: `php artisan migrate`
   - Seed data: `php artisan db:seed`

3. **Storage Setup**
   - Link storage: `php artisan storage:link`
   - Set proper permissions for storage folder

4. **Optimization**
   - Config cache: `php artisan config:cache`
   - Route cache: `php artisan route:cache`
   - View cache: `php artisan view:cache`

5. **Security**
   - HTTPS configuration
   - CSRF protection enabled
   - Rate limiting setup

---

Rancangan ini memberikan struktur lengkap untuk website rental mobil yang scalable dan maintainable. Setiap komponen dirancang untuk bekerja secara terintegrasi dengan best practices Laravel.