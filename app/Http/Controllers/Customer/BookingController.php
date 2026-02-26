<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Flight;
use App\Models\FlightSeat;
use App\Models\Passport;
use App\Models\Payment;
use App\Services\BookingService;
use App\Services\PassportOCRService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected BookingService $bookingService;
    protected PassportOCRService $ocrService;

    public function __construct(BookingService $bookingService, PassportOCRService $ocrService)
    {
        $this->bookingService = $bookingService;
        $this->ocrService = $ocrService;
    }

    public function selectFlight(Request $request)
    {
        $request->validate([
            'flight_id' => 'required|exists:flights,id',
            'flight_seat_id' => 'required|exists:flight_seats,id',
            'return_flight_id' => 'nullable|exists:flights,id',
            'return_flight_seat_id' => 'nullable|exists:flight_seats,id',
            'trip_type' => 'required|in:one_way,round_trip',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'infants' => 'nullable|integer|min:0',
        ]);

        // Store selection in session
        session(['booking_selection' => $request->all()]);

        // Check if user is logged in
        if (!auth()->check()) {
            return redirect()->route('customer.login')->with('message', __('messages.login_to_book'));
        }

        return redirect()->route('booking.passenger-details');
    }

    public function passengerDetails()
    {
        $selection = session('booking_selection');
        if (!$selection) return redirect()->route('home');

        $flight = Flight::with(['airline', 'departureAirport', 'arrivalAirport'])->findOrFail($selection['flight_id']);
        $seat = FlightSeat::findOrFail($selection['flight_seat_id']);

        $returnFlight = null;
        $returnSeat = null;
        if (($selection['trip_type'] ?? 'one_way') === 'round_trip' && !empty($selection['return_flight_id'])) {
            $returnFlight = Flight::with(['airline', 'departureAirport', 'arrivalAirport'])->find($selection['return_flight_id']);
            $returnSeat = !empty($selection['return_flight_seat_id']) ? FlightSeat::find($selection['return_flight_seat_id']) : null;
        }

        $customer = null;
        $passports = collect();
        if (auth()->check()) {
            $customer = Customer::where('user_id', auth()->id())->first();
            if ($customer) {
                $passports = $customer->passports;
            }
        }

        $totalPassengers = ($selection['adults'] ?? 1) + ($selection['children'] ?? 0) + ($selection['infants'] ?? 0);

        return view('booking.passenger-details', compact(
            'selection', 'flight', 'seat', 'returnFlight', 'returnSeat',
            'customer', 'passports', 'totalPassengers'
        ));
    }

    public function uploadPassport(Request $request)
    {
        $request->validate([
            'passport_image' => 'required|image|max:5120',
        ]);

        $result = $this->ocrService->extractFromImage($request->file('passport_image'));

        return response()->json($result);
    }

    public function storePassengerData(Request $request)
    {
        $request->validate([
            'passengers' => 'required|array|min:1',
            'passengers.*.first_name' => 'required|string|max:255',
            'passengers.*.last_name' => 'required|string|max:255',
            'passengers.*.passport_number' => 'required|string|max:50',
            'passengers.*.date_of_birth' => 'required|date',
            'passengers.*.gender' => 'required|in:male,female',
            'passengers.*.nationality' => 'required|string|max:100',
            'passengers.*.type' => 'required|in:adult,child,infant',
            'whatsapp' => 'required|string',
            'whatsapp_country_code' => 'required|string',
            'has_visa' => 'nullable|boolean',
        ]);

        $selection = session('booking_selection');
        if (!$selection) return redirect()->route('home');

        // Create or update customer
        $customer = Customer::firstOrCreate(
            ['user_id' => auth()->id()],
            [
                'first_name' => $request->passengers[0]['first_name'],
                'last_name' => $request->passengers[0]['last_name'],
                'email' => auth()->user()->email,
                'whatsapp' => $request->whatsapp,
                'whatsapp_country_code' => $request->whatsapp_country_code,
            ]
        );

        // Save passport data
        foreach ($request->passengers as $passengerData) {
            if (!empty($passengerData['passport_number'])) {
                Passport::updateOrCreate(
                    ['customer_id' => $customer->id, 'passport_number' => $passengerData['passport_number']],
                    [
                        'full_name' => $passengerData['first_name'] . ' ' . $passengerData['last_name'],
                        'first_name' => $passengerData['first_name'],
                        'last_name' => $passengerData['last_name'],
                        'date_of_birth' => $passengerData['date_of_birth'],
                        'gender' => $passengerData['gender'],
                        'nationality' => $passengerData['nationality'],
                        'expiry_date' => $passengerData['passport_expiry'] ?? null,
                        'passport_image' => $passengerData['passport_image_path'] ?? null,
                    ]
                );
            }
        }

        // Store passenger data in session
        session([
            'booking_passengers' => $request->passengers,
            'booking_customer_id' => $customer->id,
            'booking_whatsapp' => $request->whatsapp,
            'booking_has_visa' => $request->boolean('has_visa'),
        ]);

        return redirect()->route('booking.payment');
    }

    public function payment()
    {
        $selection = session('booking_selection');
        $passengers = session('booking_passengers');
        if (!$selection || !$passengers) return redirect()->route('home');

        $flight = Flight::with(['airline', 'departureAirport', 'arrivalAirport'])->findOrFail($selection['flight_id']);
        $seat = FlightSeat::findOrFail($selection['flight_seat_id']);

        $returnFlight = null;
        $returnSeat = null;
        if (!empty($selection['return_flight_id'])) {
            $returnFlight = Flight::with(['airline', 'departureAirport', 'arrivalAirport'])->find($selection['return_flight_id']);
            $returnSeat = !empty($selection['return_flight_seat_id']) ? FlightSeat::find($selection['return_flight_seat_id']) : null;
        }

        // Calculate total
        $adultTotal = ($selection['adults'] ?? 1) * $seat->adult_price;
        $childTotal = ($selection['children'] ?? 0) * $seat->child_price;
        $infantTotal = ($selection['infants'] ?? 0) * $seat->infant_price;
        $totalPrice = $adultTotal + $childTotal + $infantTotal;

        if ($returnSeat) {
            $totalPrice += ($selection['adults'] ?? 1) * $returnSeat->adult_price;
            $totalPrice += ($selection['children'] ?? 0) * $returnSeat->child_price;
            $totalPrice += ($selection['infants'] ?? 0) * $returnSeat->infant_price;
        }

        $paymentGateways = \App\Models\PaymentGateway::where('status', 'active')->get();

        return view('booking.payment', compact(
            'selection', 'passengers', 'flight', 'seat', 'returnFlight', 'returnSeat',
            'totalPrice', 'paymentGateways'
        ));
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'payment_gateway_id' => 'required|exists:payment_gateways,id',
            'receipt_image' => 'nullable|image|max:5120',
        ]);

        $selection = session('booking_selection');
        $passengers = session('booking_passengers');
        $customerId = session('booking_customer_id');

        if (!$selection || !$passengers || !$customerId) {
            return redirect()->route('home');
        }

        try {
            // Create booking
            $booking = $this->bookingService->createBooking([
                'customer_id' => $customerId,
                'flight_id' => $selection['flight_id'],
                'flight_seat_id' => $selection['flight_seat_id'],
                'return_flight_id' => $selection['return_flight_id'] ?? null,
                'return_flight_seat_id' => $selection['return_flight_seat_id'] ?? null,
                'trip_type' => $selection['trip_type'] ?? 'one_way',
                'adults' => $selection['adults'] ?? 1,
                'children' => $selection['children'] ?? 0,
                'infants' => $selection['infants'] ?? 0,
                'has_visa' => session('booking_has_visa', false),
            ]);

            // Save passengers
            foreach ($passengers as $passenger) {
                $booking->passengers()->create($passenger);
            }

            // Process payment
            $receiptPath = null;
            if ($request->hasFile('receipt_image')) {
                $receiptPath = $request->file('receipt_image')->store('receipts', 'public');
            }

            Payment::create([
                'booking_id' => $booking->id,
                'payment_gateway_id' => $request->payment_gateway_id,
                'amount' => $booking->total_price,
                'currency' => $booking->currency,
                'method' => $request->payment_method ?? 'other',
                'status' => 'pending',
                'receipt_image' => $receiptPath,
            ]);

            // Clear session
            session()->forget(['booking_selection', 'booking_passengers', 'booking_customer_id', 'booking_whatsapp', 'booking_has_visa']);

            return redirect()->route('booking.confirmation', $booking->id);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function confirmation(Booking $booking)
    {
        $booking->load(['flight.airline', 'flight.departureAirport', 'flight.arrivalAirport', 'passengers', 'payments', 'customer']);
        return view('booking.confirmation', compact('booking'));
    }

    public function myBookings()
    {
        $customer = Customer::where('user_id', auth()->id())->first();
        $bookings = $customer ? $customer->bookings()->with(['flight.airline', 'flight.departureAirport', 'flight.arrivalAirport'])->latest()->paginate(10) : collect();

        return view('booking.my-bookings', compact('bookings'));
    }
}
