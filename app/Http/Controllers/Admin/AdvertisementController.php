<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Customer;
use App\Services\WhatsappService;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    protected WhatsappService $whatsappService;

    public function __construct(WhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function index()
    {
        $ads = Advertisement::latest()->paginate(15);
        return view('admin.advertisements.index', compact('ads'));
    }

    public function create()
    {
        return view('admin.advertisements.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title_en' => 'nullable|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image' => 'required|image|max:5120',
            'url' => 'nullable|url',
            'type' => 'required|in:banner,whatsapp,popup',
            'position' => 'required|in:home_top,home_bottom,sidebar',
            'sort_order' => 'nullable|integer',
            'status' => 'required|in:active,inactive',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data['image'] = $request->file('image')->store('advertisements', 'public');
        $data['created_by'] = auth()->id();

        $ad = Advertisement::create($data);

        // If WhatsApp ad, send to all customers
        if ($data['type'] === 'whatsapp') {
            $this->sendWhatsappAd($ad);
        }

        return redirect()->route('admin.advertisements.index')->with('success', __('messages.created_successfully'));
    }

    public function edit(Advertisement $advertisement)
    {
        return view('admin.advertisements.edit', compact('advertisement'));
    }

    public function update(Request $request, Advertisement $advertisement)
    {
        $data = $request->validate([
            'title_en' => 'nullable|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'url' => 'nullable|url',
            'type' => 'required|in:banner,whatsapp,popup',
            'position' => 'required|in:home_top,home_bottom,sidebar',
            'sort_order' => 'nullable|integer',
            'status' => 'required|in:active,inactive',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('advertisements', 'public');
        }

        $advertisement->update($data);
        return redirect()->route('admin.advertisements.index')->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Advertisement $advertisement)
    {
        $advertisement->delete();
        return redirect()->route('admin.advertisements.index')->with('success', __('messages.deleted_successfully'));
    }

    protected function sendWhatsappAd(Advertisement $ad): void
    {
        $customers = Customer::whereNotNull('whatsapp')->orWhereNotNull('phone')->get();

        foreach ($customers as $customer) {
            $phone = $customer->whatsapp ?? $customer->phone;
            if ($phone) {
                $message = ($ad->title_en ?? '') . "\n" . ($ad->description_en ?? '');
                if ($ad->url) {
                    $message .= "\n" . $ad->url;
                }
                $this->whatsappService->sendMessage($phone, $message, $customer->full_name, 'advertisement', $ad->id);
            }
        }
    }
}
