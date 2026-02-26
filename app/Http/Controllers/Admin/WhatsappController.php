<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappGateway;
use App\Models\WhatsappMessage;
use App\Services\WhatsappService;
use Illuminate\Http\Request;

class WhatsappController extends Controller
{
    public function index()
    {
        $gateways = WhatsappGateway::all();
        $messages = WhatsappMessage::with('gateway')->latest()->paginate(20);
        return view('admin.whatsapp.index', compact('gateways', 'messages'));
    }

    public function createGateway()
    {
        return view('admin.whatsapp.gateway-form');
    }

    public function storeGateway(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'api_url' => 'required|url',
            'api_key' => 'nullable|string',
            'session_name' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'status' => 'required|in:active,inactive,connecting',
        ]);

        WhatsappGateway::create($data);
        return redirect()->route('admin.whatsapp.index')->with('success', __('messages.created_successfully'));
    }

    public function editGateway(WhatsappGateway $gateway)
    {
        return view('admin.whatsapp.gateway-form', compact('gateway'));
    }

    public function updateGateway(Request $request, WhatsappGateway $gateway)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'api_url' => 'required|url',
            'api_key' => 'nullable|string',
            'session_name' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'status' => 'required|in:active,inactive,connecting',
        ]);

        $gateway->update($data);
        return redirect()->route('admin.whatsapp.index')->with('success', __('messages.updated_successfully'));
    }

    public function toggleGateway(WhatsappGateway $gateway)
    {
        $gateway->update(['status' => $gateway->status === 'active' ? 'inactive' : 'active']);
        return back()->with('success', __('messages.status_updated'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        $service = new WhatsappService();
        $service->sendMessage($request->phone, $request->message);

        return back()->with('success', __('messages.message_sent'));
    }

    public function messages(Request $request)
    {
        $query = WhatsappMessage::with('gateway');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }

        $messages = $query->latest()->paginate(20);
        return view('admin.whatsapp.messages', compact('messages'));
    }
}
