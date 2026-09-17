<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

use App\Http\Requests\Auth\LoginRequest;
use App\Actions\Auth\AttemptLoginAction;
use App\Actions\Auth\LogoutAction;
use App\Models\User;
use App\Models\Role;
use App\Services\CartService;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function login(LoginRequest $request, AttemptLoginAction $attemptLogin, CartService $cartService)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if ($attemptLogin->run($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Merge guest cart if any
            $cartService->mergeSessionCartToUser($user);

            // Redirect admin to dashboard, customer to intended storefront or home
            if ($user->hasRole('admin')) {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Selamat datang kembali, Administrator!');
            }

            return redirect()->intended(route('home'))->with('success', "Selamat datang kembali, {$user->name}!");
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function register(Request $request, CartService $cartService)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        // Attach customer role
        $customerRole = Role::where('slug', 'customer')->first();
        if ($customerRole) {
            $user->roles()->attach($customerRole->id);
        }

        Auth::login($user);
        $request->session()->regenerate();

        // Merge cart
        $cartService->mergeSessionCartToUser($user);

        return redirect()->intended(route('home'))->with('success', 'Pendaftaran akun berhasil! Selamat berbelanja.');
    }

    /**
     * AJAX Login for modal popup in checkout or storefront.
     */
    public function ajaxLogin(Request $request, AttemptLoginAction $attemptLogin, CartService $cartService)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if ($attemptLogin->run($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            $cartService->mergeSessionCartToUser($user);

            $intendedUrl = session()->pull('url.intended', route('checkout.index'));
            if ($user->hasRole('admin')) {
                $intendedUrl = route('admin.dashboard');
            }

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil!',
                'redirect' => $intendedUrl,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email atau kata sandi tidak sesuai.',
        ], 422);
    }

    /**
     * AJAX Register for modal popup in checkout or storefront.
     */
    public function ajaxRegister(Request $request, CartService $cartService)
    {
        $validator = \Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        $customerRole = Role::where('slug', 'customer')->first();
        if ($customerRole) {
            $user->roles()->attach($customerRole->id);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $cartService->mergeSessionCartToUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil!',
            'redirect' => route('checkout.index'),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function logout(Request $request, LogoutAction $logout)
    {
        $logout->run();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah berhasil keluar.');
    }
}
