<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->settings as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', __('messages.settings_updated'));
    }

    public function backup()
    {
        $dbPath = database_path('database.sqlite');
        $backupPath = storage_path('app/backups/backup_' . date('Y_m_d_His') . '.sqlite');

        if (!is_dir(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        copy($dbPath, $backupPath);

        return back()->with('success', __('messages.backup_created'));
    }

    public function users(Request $request)
    {
        $query = \App\Models\User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('username', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15);
        return view('admin.settings.users', compact('users'));
    }

    public function userActivityLog(\App\Models\User $user)
    {
        $logs = $user->activityLogs()->latest()->paginate(20);
        return view('admin.settings.user-log', compact('user', 'logs'));
    }

    public function toggleUserStatus(\App\Models\User $user)
    {
        $user->update(['status' => $user->status === 'active' ? 'suspended' : 'active']);
        return back()->with('success', __('messages.status_updated'));
    }

    public function updateUserPassword(Request $request, \App\Models\User $user)
    {
        $request->validate(['password' => 'required|string|min:6']);
        $user->update(['password' => bcrypt($request->password)]);
        return back()->with('success', __('messages.password_updated'));
    }

    public function customers(Request $request)
    {
        $query = \App\Models\Customer::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(15);
        return view('admin.settings.customers', compact('customers'));
    }

    public function financial()
    {
        $accounts = \App\Models\FinancialAccount::with(['provider', 'agent', 'paymentGateway'])->get();
        $totalBalance = $accounts->sum('balance');
        $totalCommissions = $accounts->where('account_type', 'commission')->sum('balance');

        return view('admin.settings.financial', compact('accounts', 'totalBalance', 'totalCommissions'));
    }

    public function financialTransactions(\App\Models\FinancialAccount $account)
    {
        $transactions = $account->transactions()->latest()->paginate(20);
        return view('admin.settings.transactions', compact('account', 'transactions'));
    }
}
