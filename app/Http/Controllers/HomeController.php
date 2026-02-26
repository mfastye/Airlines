<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Airport;
use App\Services\SmartSearchService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected SmartSearchService $searchService;

    public function __construct(SmartSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index()
    {
        $airports = Airport::where('status', 'active')->get();
        $ads = Advertisement::active()->where('position', 'home_top')->orderBy('sort_order')->get();
        $popularRoutes = $this->searchService->getPopularRoutes(6);

        return view('home', compact('airports', 'ads', 'popularRoutes'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'departure_airport_id' => 'required|exists:airports,id',
            'arrival_airport_id' => 'required|exists:airports,id|different:departure_airport_id',
            'departure_date' => 'required|date|after_or_equal:today',
            'return_date' => 'nullable|date|after:departure_date',
            'trip_type' => 'required|in:one_way,round_trip',
            'adults' => 'required|integer|min:1|max:9',
            'children' => 'nullable|integer|min:0|max:9',
            'class' => 'required|in:economy,business,first',
        ]);

        $results = $this->searchService->search($request->all());
        $airports = Airport::where('status', 'active')->get();
        $ads = Advertisement::active()->where('position', 'home_top')->orderBy('sort_order')->get();

        return view('search-results', compact('results', 'airports', 'ads'));
    }

    public function searchAirports(Request $request)
    {
        $query = $request->get('q', '');
        if (strlen($query) < 2) return response()->json([]);

        $airports = $this->searchService->searchAirports($query);
        return response()->json($airports);
    }

    public function setLanguage(Request $request)
    {
        $lang = $request->get('lang', 'ar');
        if (in_array($lang, ['ar', 'en'])) {
            session(['locale' => $lang]);
            if (auth()->check()) {
                auth()->user()->update(['language' => $lang]);
            }
        }
        return back();
    }

    public function toggleDarkMode(Request $request)
    {
        $darkMode = !session('dark_mode', false);
        session(['dark_mode' => $darkMode]);
        if (auth()->check()) {
            auth()->user()->update(['dark_mode' => $darkMode]);
        }
        return back();
    }

    public function adClick(Advertisement $ad)
    {
        $ad->increment('clicks');
        if ($ad->url) {
            return redirect($ad->url);
        }
        return back();
    }
}
