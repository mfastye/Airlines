<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\FinancialAccount;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AgentController extends Controller
{
    public function index(Request $request)
    {
        $query = Agent::with(['provider'])->withCount('bookings');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('provider_id')) {
            $query->where('provider_id', $request->provider_id);
        }

        $agents = $query->latest()->paginate(15);
        $providers = Provider::where('status', 'active')->get();
        return view('admin.agents.index', compact('agents', 'providers'));
    }

    public function create()
    {
        $providers = Provider::where('status', 'active')->get();
        return view('admin.agents.create', compact('providers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'phone_country_code' => 'required|string|max:10',
            'whatsapp' => 'nullable|string|max:20',
            'whatsapp_country_code' => 'nullable|string|max:10',
            'email' => 'nullable|email',
            'provider_id' => 'nullable|exists:providers,id',
            'commission_amount' => 'nullable|numeric|min:0',
            'commission_type' => 'nullable|in:fixed,percentage',
            'status' => 'required|in:active,suspended,pending',
            'api_enabled' => 'nullable|boolean',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
        ]);

        if ($request->boolean('api_enabled')) {
            $data['api_key'] = Str::random(32);
            $data['api_secret'] = Str::random(64);
        }

        $agent = Agent::create($data);

        // Create user account
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role' => 'agent',
            'agent_id' => $agent->id,
            'provider_id' => $data['provider_id'] ?? null,
            'phone' => $data['phone'],
            'status' => $data['status'] === 'active' ? 'active' : 'suspended',
        ]);

        // Create financial account
        FinancialAccount::create([
            'account_name_en' => $data['name'] . ' Agent Account',
            'account_name_ar' => $data['name'] . ' حساب الوكيل',
            'account_type' => 'agent',
            'agent_id' => $agent->id,
            'currency' => 'USD',
        ]);

        return redirect()->route('admin.agents.index')->with('success', __('messages.created_successfully'));
    }

    public function show(Agent $agent)
    {
        $agent->load(['provider', 'users', 'financialAccount.transactions', 'bookings.customer']);
        return view('admin.agents.show', compact('agent'));
    }

    public function edit(Agent $agent)
    {
        $providers = Provider::where('status', 'active')->get();
        return view('admin.agents.edit', compact('agent', 'providers'));
    }

    public function update(Request $request, Agent $agent)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'phone_country_code' => 'required|string|max:10',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'provider_id' => 'nullable|exists:providers,id',
            'commission_amount' => 'nullable|numeric|min:0',
            'commission_type' => 'nullable|in:fixed,percentage',
            'status' => 'required|in:active,suspended,pending',
            'api_enabled' => 'nullable|boolean',
        ]);

        $agent->update($data);
        $agent->users()->update(['status' => $data['status'] === 'active' ? 'active' : 'suspended']);

        return redirect()->route('admin.agents.index')->with('success', __('messages.updated_successfully'));
    }

    public function addBalance(Request $request, Agent $agent)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        $account = $agent->financialAccount;
        if ($account) {
            $account->credit(
                $request->amount,
                $request->description ?? 'Balance top-up',
                $request->description ?? 'شحن رصيد',
                auth()->id()
            );
            $agent->increment('balance', $request->amount);
        }

        return back()->with('success', __('messages.balance_added'));
    }

    public function destroy(Agent $agent)
    {
        if ($agent->bookings()->exists()) {
            return back()->with('error', __('messages.cannot_delete_agent'));
        }
        $agent->users()->delete();
        $agent->delete();
        return redirect()->route('admin.agents.index')->with('success', __('messages.deleted_successfully'));
    }
}
