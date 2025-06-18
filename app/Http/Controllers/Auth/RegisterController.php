<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/customer/dashboard';

    protected $notificationService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('guest');
        $this->notificationService = $notificationService;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
                    ? new JsonResponse([], 201)
                    : redirect($this->redirectPath());
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'identity_number' => ['required', 'string', 'max:20', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.string' => 'Format nama tidak valid.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.string' => 'Format email tidak valid.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah terdaftar.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.string' => 'Format nomor telepon tidak valid.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'address.required' => 'Alamat wajib diisi.',
            'address.string' => 'Format alamat tidak valid.',
            'address.max' => 'Alamat maksimal 500 karakter.',
            'identity_number.required' => 'Nomor KTP/SIM wajib diisi.',
            'identity_number.string' => 'Format nomor identitas tidak valid.',
            'identity_number.max' => 'Nomor identitas maksimal 20 karakter.',
            'identity_number.unique' => 'Nomor identitas sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.string' => 'Format password tidak valid.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'identity_number' => $data['identity_number'],
            'role' => 'customer',
            'password' => Hash::make($data['password']),
        ]);

        // Create welcome notification for new user
        $this->notificationService->createNotification(
            $user->id,
            'Selamat Datang!',
            'Terima kasih telah mendaftar. Sekarang Anda dapat mulai menyewa mobil dengan mudah.',
            'general'
        );

        // Notify admins about new registration
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $this->notificationService->createNotification(
                $admin->id,
                'Customer Baru Terdaftar',
                "Customer baru dengan nama {$user->name} telah mendaftar.",
                'general'
            );
        }

        return $user;
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        return redirect()->route('customer.dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang di sistem rental mobil kami.');
    }
}
