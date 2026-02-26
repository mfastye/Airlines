<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use Illuminate\Http\Request;

class AirlineController extends Controller
{
    public function index(Request $request)
    {
        $query = Airline::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'LIKE', "%{$search}%")
                  ->orWhere('name_ar', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $airlines = $query->latest()->paginate(15);
        return view('admin.airlines.index', compact('airlines'));
    }

    public function create()
    {
        return view('admin.airlines.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:airlines,code',
            'iata_code' => 'nullable|string|max:5',
            'logo' => 'nullable|image|max:2048',
            'country_en' => 'nullable|string|max:255',
            'country_ar' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'website' => 'nullable|url',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('airlines', 'public');
        }

        Airline::create($data);

        return redirect()->route('admin.airlines.index')->with('success', __('messages.created_successfully'));
    }

    public function edit(Airline $airline)
    {
        return view('admin.airlines.edit', compact('airline'));
    }

    public function update(Request $request, Airline $airline)
    {
        $data = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:airlines,code,' . $airline->id,
            'iata_code' => 'nullable|string|max:5',
            'logo' => 'nullable|image|max:2048',
            'country_en' => 'nullable|string|max:255',
            'country_ar' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'website' => 'nullable|url',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('airlines', 'public');
        }

        $airline->update($data);

        return redirect()->route('admin.airlines.index')->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Airline $airline)
    {
        if ($airline->flights()->exists()) {
            return back()->with('error', __('messages.cannot_delete_has_flights'));
        }
        $airline->delete();
        return redirect()->route('admin.airlines.index')->with('success', __('messages.deleted_successfully'));
    }
}
