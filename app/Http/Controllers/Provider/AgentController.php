<?php

namespace App\Http\Controllers\Provider;

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
    protected function getProvider(): Provider
    {
        $provider = Provider::find(auth()->user()->provider_id);
        if (!$provider) abort(403);
        return $provider;
    }

    public function index()
    {
        $provider = $this->getProvider();
        $agents = Agent::with('users')->where('provider_id', $provider->id)->withCount('bookings')->paginate(15);
        return view('provider.agents.index', compact('agents', 'provider'));
    }

    public function create()
    {
        return view('provider.agents.create');
    }

    public function store(Request $request)
    {
        $provider = $this->getProvider();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'phone_country_code' => 'required|string|max:10',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'commission_amount' => 'nullable|numeric|min:0',
            'commission_type' => 'nullable|in:fixed,percentage',
            'api_enabled' => 'nullable|boolean',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
        ]);

        $data['provider_id'] = $provider->id;
        $data['status'] = 'active';

        if ($request->boolean('api_enabled')) {
            $data['api_key'] = Str::random(32);
            $data['api_secret'] = Str::random(64);
        }

        $agent = Agent::create($data);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role' => 'agent',
            'agent_id' => $agent->id,
            'provider_id' => $provider->id,
            'phone' => $data['phone'],
            'status' => 'active',
        ]);

        FinancialAccount::create([
            'account_name_en' => $data['name'] . ' Agent Account',
            'account_name_ar' => $data['name'] . ' حساب الوكيل',
            'account_type' => 'agent',
            'agent_id' => $agent->id,
            'currency' => 'USD',
        ]);

        return redirect()->route('provider.agents.index')->with('success', __('messages.created_successfully'));
    }

    public function show(Agent $agent)
    {
        $provider = $this->getProvider();
        if ($agent->provider_id !== $provider->id) abort(403);

        $agent->load(['financialAccount.transactions', 'bookings.customer']);
        return view('provider.agents.show', compact('agent', 'provider'));
    }

    public function addBalance(Request $request, Agent $agent)
    {
        $provider = $this->getProvider();
        if ($agent->provider_id !== $provider->id) abort(403);

        $request->validate(['amount' => 'required|numeric|min:0.01']);

        $account = $agent->financialAccount;
        if ($account) {
            $account->credit($request->amount, 'Balance top-up by provider', 'شحن رصيد من المزود', auth()->id());
            $agent->increment('balance', $request->amount);
        }

        return back()->with('success', __('messages.balance_added'));
    }

    public function toggleStatus(Agent $agent)
    {
        $provider = $this->getProvider();
        if ($agent->provider_id !== $provider->id) abort(403);

        $newStatus = $agent->status === 'active' ? 'suspended' : 'active';
        $agent->update(['status' => $newStatus]);
        $agent->users()->update(['status' => $newStatus]);

        return back()->with('success', __('messages.status_updated'));
    }
}
