<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['username' => __('messages.invalid_credentials')])->withInput();
        }

        if ($user->status !== 'active') {
            return back()->withErrors(['username' => __('messages.account_suspended')])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        UserActivityLog::log($user->id, 'login', 'User logged in');

        return $this->redirectByRole($user);
    }

    public function logout(Request $request)
    {
        if (auth()->check()) {
            UserActivityLog::log(auth()->id(), 'logout', 'User logged out');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    protected function redirectByRole(User $user)
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'provider' => redirect()->route('provider.dashboard'),
            'agent' => redirect()->route('agent.dashboard'),
            'employee' => redirect()->route('admin.dashboard'),
            default => redirect()->route('home'),
        };
    }

    public function showCustomerLogin()
    {
        return view('auth.customer-login');
    }

    public function customerRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'status' => 'active',
        ]);

        \App\Models\Customer::create([
            'user_id' => $user->id,
            'first_name' => explode(' ', $request->name)[0],
            'last_name' => explode(' ', $request->name, 2)[1] ?? '',
            'email' => $request->email,
        ]);

        Auth::login($user);

        return redirect()->intended(route('home'));
    }
}
