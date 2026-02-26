<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use App\Models\FinancialAccount;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProviderController extends Controller
{
    public function index(Request $request)
    {
        $query = Provider::withCount(['agents', 'bookings', 'flightSeats']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('business_name_en', 'LIKE', "%{$search}%")
                  ->orWhere('business_name_ar', 'LIKE', "%{$search}%")
                  ->orWhere('contact_person', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $providers = $query->latest()->paginate(15);
        return view('admin.providers.index', compact('providers'));
    }

    public function create()
    {
        $airlines = Airline::where('status', 'active')->get();
        return view('admin.providers.create', compact('airlines'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_name_en' => 'required|string|max:255',
            'business_name_ar' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'phone_country_code' => 'required|string|max:10',
            'whatsapp' => 'nullable|string|max:20',
            'whatsapp_country_code' => 'nullable|string|max:10',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'commission_amount' => 'required|numeric|min:0',
            'commission_type' => 'required|in:fixed,percentage',
            'allowed_airlines' => 'nullable|array',
            'allowed_airlines.*' => 'exists:airlines,id',
            'status' => 'required|in:active,suspended,pending',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('providers', 'public');
        }

        $provider = Provider::create($data);

        // Create user account for provider
        $user = User::create([
            'name' => $data['contact_person'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role' => 'provider',
            'provider_id' => $provider->id,
            'phone' => $data['phone'],
            'phone_country_code' => $data['phone_country_code'],
            'whatsapp' => $data['whatsapp'] ?? null,
            'status' => $data['status'] === 'active' ? 'active' : 'suspended',
        ]);

        // Create financial account
        FinancialAccount::create([
            'account_name_en' => $data['business_name_en'] . ' Account',
            'account_name_ar' => $data['business_name_ar'] . ' حساب',
            'account_type' => 'provider',
            'provider_id' => $provider->id,
            'currency' => 'USD',
        ]);

        return redirect()->route('admin.providers.index')->with('success', __('messages.created_successfully'));
    }

    public function show(Provider $provider)
    {
        $provider->load(['agents', 'users', 'financialAccount.transactions', 'flightSeats.flight.airline']);
        return view('admin.providers.show', compact('provider'));
    }

    public function edit(Provider $provider)
    {
        $airlines = Airline::where('status', 'active')->get();
        return view('admin.providers.edit', compact('provider', 'airlines'));
    }

    public function update(Request $request, Provider $provider)
    {
        $data = $request->validate([
            'business_name_en' => 'required|string|max:255',
            'business_name_ar' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'phone_country_code' => 'required|string|max:10',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'commission_amount' => 'required|numeric|min:0',
            'commission_type' => 'required|in:fixed,percentage',
            'allowed_airlines' => 'nullable|array',
            'status' => 'required|in:active,suspended,pending',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('providers', 'public');
        }

        $provider->update($data);

        // Update provider user status
        $provider->users()->update([
            'status' => $data['status'] === 'active' ? 'active' : 'suspended',
        ]);

        return redirect()->route('admin.providers.index')->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Provider $provider)
    {
        if ($provider->bookings()->exists() || $provider->financialAccount?->balance != 0) {
            return back()->with('error', __('messages.cannot_delete_provider'));
        }
        $provider->users()->delete();
        $provider->delete();
        return redirect()->route('admin.providers.index')->with('success', __('messages.deleted_successfully'));
    }
}
