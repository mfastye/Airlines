<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use Illuminate\Http\Request;

class AirportController extends Controller
{
    public function index(Request $request)
    {
        $query = Airport::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'LIKE', "%{$search}%")
                  ->orWhere('name_ar', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('city_en', 'LIKE', "%{$search}%")
                  ->orWhere('city_ar', 'LIKE', "%{$search}%");
            });
        }

        $airports = $query->orderBy('name_en')->paginate(15);
        return view('admin.airports.index', compact('airports'));
    }

    public function create()
    {
        return view('admin.airports.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:airports,code',
            'iata_code' => 'nullable|string|max:5',
            'city_en' => 'required|string|max:255',
            'city_ar' => 'required|string|max:255',
            'country_en' => 'required|string|max:255',
            'country_ar' => 'required|string|max:255',
            'timezone' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Airport::create($data);
        return redirect()->route('admin.airports.index')->with('success', __('messages.created_successfully'));
    }

    public function edit(Airport $airport)
    {
        return view('admin.airports.edit', compact('airport'));
    }

    public function update(Request $request, Airport $airport)
    {
        $data = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:airports,code,' . $airport->id,
            'iata_code' => 'nullable|string|max:5',
            'city_en' => 'required|string|max:255',
            'city_ar' => 'required|string|max:255',
            'country_en' => 'required|string|max:255',
            'country_ar' => 'required|string|max:255',
            'timezone' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $airport->update($data);
        return redirect()->route('admin.airports.index')->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Airport $airport)
    {
        if ($airport->departureFlights()->exists() || $airport->arrivalFlights()->exists()) {
            return back()->with('error', __('messages.cannot_delete_airport'));
        }
        $airport->delete();
        return redirect()->route('admin.airports.index')->with('success', __('messages.deleted_successfully'));
    }
}
