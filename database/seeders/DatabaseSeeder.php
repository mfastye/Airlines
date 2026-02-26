<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Customer;
use App\Models\FinancialAccount;
use App\Models\Flight;
use App\Models\FlightSeat;
use App\Models\Passport;
use App\Models\PaymentGateway;
use App\Models\Provider;
use App\Models\Setting;
use App\Models\User;
use App\Models\WhatsappGateway;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === 1. ADMIN USER ===
        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@skybooker.com',
            'password' => Hash::make('admin123'),
            'username' => 'admin',
            'role' => 'admin',
            'phone' => '966500000001',
            'phone_country_code' => '+966',
            'status' => 'active',
            'language' => 'ar',
        ]);

        // === 2. AIRLINES ===
        $airlines = [
            ['name_en' => 'Qatar Airways', 'name_ar' => 'الخطوط الجوية القطرية', 'code' => 'QR', 'iata_code' => 'QR', 'country_en' => 'Qatar', 'country_ar' => 'قطر', 'website' => 'https://www.qatarairways.com'],
            ['name_en' => 'Saudi Airlines', 'name_ar' => 'الخطوط السعودية', 'code' => 'SV', 'iata_code' => 'SV', 'country_en' => 'Saudi Arabia', 'country_ar' => 'السعودية', 'website' => 'https://www.saudia.com'],
            ['name_en' => 'Emirates', 'name_ar' => 'طيران الإمارات', 'code' => 'EK', 'iata_code' => 'EK', 'country_en' => 'UAE', 'country_ar' => 'الإمارات', 'website' => 'https://www.emirates.com'],
            ['name_en' => 'Turkish Airlines', 'name_ar' => 'الخطوط التركية', 'code' => 'TK', 'iata_code' => 'TK', 'country_en' => 'Turkey', 'country_ar' => 'تركيا', 'website' => 'https://www.turkishairlines.com'],
            ['name_en' => 'Egypt Air', 'name_ar' => 'مصر للطيران', 'code' => 'MS', 'iata_code' => 'MS', 'country_en' => 'Egypt', 'country_ar' => 'مصر', 'website' => 'https://www.egyptair.com'],
            ['name_en' => 'Flynas', 'name_ar' => 'طيران ناس', 'code' => 'XY', 'iata_code' => 'XY', 'country_en' => 'Saudi Arabia', 'country_ar' => 'السعودية', 'website' => 'https://www.flynas.com'],
            ['name_en' => 'Kuwait Airways', 'name_ar' => 'الخطوط الجوية الكويتية', 'code' => 'KU', 'iata_code' => 'KU', 'country_en' => 'Kuwait', 'country_ar' => 'الكويت', 'website' => 'https://www.kuwaitairways.com'],
            ['name_en' => 'Etihad Airways', 'name_ar' => 'الاتحاد للطيران', 'code' => 'EY', 'iata_code' => 'EY', 'country_en' => 'UAE', 'country_ar' => 'الإمارات', 'website' => 'https://www.etihad.com'],
        ];
        $airlineModels = [];
        foreach ($airlines as $data) {
            $airlineModels[] = Airline::create(array_merge($data, ['status' => 'active']));
        }

        // === 3. AIRPORTS ===
        $airports = [
            ['name_en' => 'King Abdulaziz International Airport', 'name_ar' => 'مطار الملك عبدالعزيز الدولي', 'code' => 'JED', 'iata_code' => 'JED', 'city_en' => 'Jeddah', 'city_ar' => 'جدة', 'country_en' => 'Saudi Arabia', 'country_ar' => 'السعودية', 'timezone' => 'Asia/Riyadh'],
            ['name_en' => 'King Khalid International Airport', 'name_ar' => 'مطار الملك خالد الدولي', 'code' => 'RUH', 'iata_code' => 'RUH', 'city_en' => 'Riyadh', 'city_ar' => 'الرياض', 'country_en' => 'Saudi Arabia', 'country_ar' => 'السعودية', 'timezone' => 'Asia/Riyadh'],
            ['name_en' => 'Hamad International Airport', 'name_ar' => 'مطار حمد الدولي', 'code' => 'DOH', 'iata_code' => 'DOH', 'city_en' => 'Doha', 'city_ar' => 'الدوحة', 'country_en' => 'Qatar', 'country_ar' => 'قطر', 'timezone' => 'Asia/Qatar'],
            ['name_en' => 'Dubai International Airport', 'name_ar' => 'مطار دبي الدولي', 'code' => 'DXB', 'iata_code' => 'DXB', 'city_en' => 'Dubai', 'city_ar' => 'دبي', 'country_en' => 'UAE', 'country_ar' => 'الإمارات', 'timezone' => 'Asia/Dubai'],
            ['name_en' => 'Cairo International Airport', 'name_ar' => 'مطار القاهرة الدولي', 'code' => 'CAI', 'iata_code' => 'CAI', 'city_en' => 'Cairo', 'city_ar' => 'القاهرة', 'country_en' => 'Egypt', 'country_ar' => 'مصر', 'timezone' => 'Africa/Cairo'],
            ['name_en' => 'Istanbul Airport', 'name_ar' => 'مطار إسطنبول', 'code' => 'IST', 'iata_code' => 'IST', 'city_en' => 'Istanbul', 'city_ar' => 'إسطنبول', 'country_en' => 'Turkey', 'country_ar' => 'تركيا', 'timezone' => 'Europe/Istanbul'],
            ['name_en' => 'Kuwait International Airport', 'name_ar' => 'مطار الكويت الدولي', 'code' => 'KWI', 'iata_code' => 'KWI', 'city_en' => 'Kuwait', 'city_ar' => 'الكويت', 'country_en' => 'Kuwait', 'country_ar' => 'الكويت', 'timezone' => 'Asia/Kuwait'],
            ['name_en' => 'Abu Dhabi International Airport', 'name_ar' => 'مطار أبوظبي الدولي', 'code' => 'AUH', 'iata_code' => 'AUH', 'city_en' => 'Abu Dhabi', 'city_ar' => 'أبوظبي', 'country_en' => 'UAE', 'country_ar' => 'الإمارات', 'timezone' => 'Asia/Dubai'],
            ['name_en' => 'Medina Prince Mohammad Airport', 'name_ar' => 'مطار الأمير محمد بن عبدالعزيز', 'code' => 'MED', 'iata_code' => 'MED', 'city_en' => 'Medina', 'city_ar' => 'المدينة المنورة', 'country_en' => 'Saudi Arabia', 'country_ar' => 'السعودية', 'timezone' => 'Asia/Riyadh'],
            ['name_en' => 'London Heathrow Airport', 'name_ar' => 'مطار لندن هيثرو', 'code' => 'LHR', 'iata_code' => 'LHR', 'city_en' => 'London', 'city_ar' => 'لندن', 'country_en' => 'United Kingdom', 'country_ar' => 'المملكة المتحدة', 'timezone' => 'Europe/London'],
        ];
        $airportModels = [];
        foreach ($airports as $data) {
            $airportModels[] = Airport::create(array_merge($data, ['status' => 'active']));
        }

        // === 4. PROVIDERS ===
        $provider1 = Provider::create([
            'business_name_en' => 'Al-Safa Travel Agency',
            'business_name_ar' => 'وكالة الصفا للسفريات',
            'contact_person' => 'Ahmed Mohammed',
            'phone' => '966500000010',
            'phone_country_code' => '+966',
            'email' => 'info@alsafa-travel.com',
            'address' => 'Jeddah, Saudi Arabia',
            'commission_amount' => 15.00,
            'commission_type' => 'fixed',
            'allowed_airlines' => json_encode([1, 2, 3, 4, 5, 6, 7, 8]),
            'status' => 'active',
            'balance' => 5000.00,
        ]);
        $provider2 = Provider::create([
            'business_name_en' => 'Gulf Star Tourism',
            'business_name_ar' => 'نجمة الخليج للسياحة',
            'contact_person' => 'Khalid Al-Rashid',
            'phone' => '966500000020',
            'phone_country_code' => '+966',
            'email' => 'info@gulfstar.com',
            'address' => 'Riyadh, Saudi Arabia',
            'commission_amount' => 10.00,
            'commission_type' => 'fixed',
            'allowed_airlines' => json_encode([1, 2, 3, 6]),
            'status' => 'active',
            'balance' => 3000.00,
        ]);
        User::create(['name' => 'Ahmed Mohammed', 'email' => 'ahmed@alsafa-travel.com', 'password' => Hash::make('provider123'), 'username' => 'alsafa', 'role' => 'provider', 'phone' => '966500000010', 'provider_id' => $provider1->id, 'status' => 'active']);
        User::create(['name' => 'Khalid Al-Rashid', 'email' => 'khalid@gulfstar.com', 'password' => Hash::make('provider123'), 'username' => 'gulfstar', 'role' => 'provider', 'phone' => '966500000020', 'provider_id' => $provider2->id, 'status' => 'active']);

        // === 5. AGENTS ===
        $agent1 = Agent::create(['name' => 'Quick Book Agency', 'phone' => '966500000030', 'phone_country_code' => '+966', 'email' => 'info@quickbook.com', 'provider_id' => $provider1->id, 'balance' => 2000.00, 'commission_amount' => 5.00, 'commission_type' => 'fixed', 'status' => 'active', 'api_enabled' => true, 'api_key' => 'ak_' . Str::random(32), 'api_secret' => 'as_' . Str::random(48)]);
        $agent2 = Agent::create(['name' => 'Travel Express', 'phone' => '966500000040', 'phone_country_code' => '+966', 'email' => 'info@travelexpress.com', 'provider_id' => $provider1->id, 'balance' => 1500.00, 'commission_amount' => 3.00, 'commission_type' => 'percentage', 'status' => 'active', 'api_enabled' => false]);
        User::create(['name' => 'Quick Book Agency', 'email' => 'agent1@quickbook.com', 'password' => Hash::make('agent123'), 'username' => 'quickbook', 'role' => 'agent', 'phone' => '966500000030', 'agent_id' => $agent1->id, 'status' => 'active']);
        User::create(['name' => 'Travel Express', 'email' => 'agent2@travelexpress.com', 'password' => Hash::make('agent123'), 'username' => 'travelexpress', 'role' => 'agent', 'phone' => '966500000040', 'agent_id' => $agent2->id, 'status' => 'active']);

        // === 6. FLIGHTS ===
        $flightData = [
            ['num' => 'QR1234', 'al' => 1, 'dep' => 0, 'arr' => 2, 'dt' => '+3 days 08:00', 'at' => '+3 days 09:30', 'dur' => 90, 'p' => $provider1->id],
            ['num' => 'SV456', 'al' => 2, 'dep' => 0, 'arr' => 1, 'dt' => '+2 days 10:00', 'at' => '+2 days 11:30', 'dur' => 90, 'p' => $provider1->id],
            ['num' => 'EK789', 'al' => 3, 'dep' => 0, 'arr' => 3, 'dt' => '+4 days 14:00', 'at' => '+4 days 17:00', 'dur' => 180, 'p' => $provider1->id],
            ['num' => 'TK101', 'al' => 4, 'dep' => 0, 'arr' => 5, 'dt' => '+5 days 06:00', 'at' => '+5 days 10:00', 'dur' => 240, 'p' => $provider2->id],
            ['num' => 'MS202', 'al' => 5, 'dep' => 1, 'arr' => 4, 'dt' => '+3 days 15:00', 'at' => '+3 days 18:00', 'dur' => 180, 'p' => $provider2->id],
            ['num' => 'XY303', 'al' => 6, 'dep' => 1, 'arr' => 0, 'dt' => '+2 days 07:00', 'at' => '+2 days 08:30', 'dur' => 90, 'p' => $provider1->id],
            ['num' => 'QR5678', 'al' => 1, 'dep' => 2, 'arr' => 9, 'dt' => '+6 days 22:00', 'at' => '+7 days 05:00', 'dur' => 420, 'p' => $provider1->id],
            ['num' => 'KU404', 'al' => 7, 'dep' => 6, 'arr' => 4, 'dt' => '+4 days 12:00', 'at' => '+4 days 15:00', 'dur' => 180, 'p' => $provider2->id],
            ['num' => 'EY505', 'al' => 8, 'dep' => 7, 'arr' => 0, 'dt' => '+3 days 09:00', 'at' => '+3 days 12:00', 'dur' => 180, 'p' => $provider1->id],
            ['num' => 'SV606', 'al' => 2, 'dep' => 0, 'arr' => 8, 'dt' => '+5 days 16:00', 'at' => '+5 days 17:00', 'dur' => 60, 'p' => $provider1->id],
            ['num' => 'QR4321', 'al' => 1, 'dep' => 2, 'arr' => 0, 'dt' => '+10 days 14:00', 'at' => '+10 days 15:30', 'dur' => 90, 'p' => $provider1->id],
            ['num' => 'EK987', 'al' => 3, 'dep' => 3, 'arr' => 0, 'dt' => '+11 days 08:00', 'at' => '+11 days 11:00', 'dur' => 180, 'p' => $provider1->id],
        ];
        $flightModels = [];
        foreach ($flightData as $fd) {
            $flight = Flight::create([
                'flight_number' => $fd['num'], 'airline_id' => $fd['al'],
                'departure_airport_id' => $airportModels[$fd['dep']]->id,
                'arrival_airport_id' => $airportModels[$fd['arr']]->id,
                'departure_time' => now()->modify($fd['dt']),
                'arrival_time' => now()->modify($fd['at']),
                'duration_minutes' => $fd['dur'], 'status' => 'active',
                'provider_id' => $fd['p'], 'created_by' => $admin->id,
            ]);
            $flightModels[] = $flight;
            FlightSeat::create(['flight_id' => $flight->id, 'class' => 'economy', 'total_seats' => 150, 'available_seats' => 145, 'adult_price' => rand(200, 500), 'child_price' => rand(150, 350), 'infant_price' => 50, 'baggage_allowance_kg' => 23, 'hand_baggage_kg' => 7, 'meal_included' => true, 'provider_id' => $fd['p'], 'status' => 'available']);
            FlightSeat::create(['flight_id' => $flight->id, 'class' => 'business', 'total_seats' => 30, 'available_seats' => 28, 'adult_price' => rand(800, 1500), 'child_price' => rand(600, 1100), 'infant_price' => 100, 'baggage_allowance_kg' => 40, 'hand_baggage_kg' => 10, 'meal_included' => true, 'wifi_available' => true, 'entertainment_available' => true, 'provider_id' => $fd['p'], 'status' => 'available']);
            FlightSeat::create(['flight_id' => $flight->id, 'class' => 'first', 'total_seats' => 10, 'available_seats' => 9, 'adult_price' => rand(2000, 4000), 'child_price' => rand(1500, 3000), 'infant_price' => 200, 'baggage_allowance_kg' => 50, 'hand_baggage_kg' => 15, 'meal_included' => true, 'wifi_available' => true, 'entertainment_available' => true, 'provider_id' => $fd['p'], 'status' => 'available']);
        }

        // === 7. CUSTOMERS ===
        $customer1 = Customer::create(['first_name' => 'Mohammed', 'last_name' => 'Al-Ahmad', 'email' => 'mohammed@example.com', 'phone' => '966500000050', 'phone_country_code' => '+966', 'whatsapp' => '966500000050', 'whatsapp_country_code' => '+966', 'date_of_birth' => '1990-05-15', 'gender' => 'male', 'nationality' => 'Saudi']);
        $cu1 = User::create(['name' => 'Mohammed Al-Ahmad', 'email' => 'mohammed@example.com', 'password' => Hash::make('customer123'), 'username' => 'mohammed', 'role' => 'customer', 'phone' => '966500000050', 'status' => 'active']);
        $customer1->update(['user_id' => $cu1->id]);
        $customer2 = Customer::create(['first_name' => 'Sara', 'last_name' => 'Al-Otaibi', 'email' => 'sara@example.com', 'phone' => '966500000060', 'phone_country_code' => '+966', 'date_of_birth' => '1995-08-20', 'gender' => 'female', 'nationality' => 'Saudi']);
        $cu2 = User::create(['name' => 'Sara Al-Otaibi', 'email' => 'sara@example.com', 'password' => Hash::make('customer123'), 'username' => 'sara', 'role' => 'customer', 'phone' => '966500000060', 'status' => 'active']);
        $customer2->update(['user_id' => $cu2->id]);

        // === 8. PASSPORTS ===
        Passport::create(['customer_id' => $customer1->id, 'passport_number' => 'A12345678', 'full_name' => 'MOHAMMED AL-AHMAD', 'first_name' => 'MOHAMMED', 'last_name' => 'AL-AHMAD', 'date_of_birth' => '1990-05-15', 'nationality' => 'Saudi', 'gender' => 'male', 'issue_date' => '2022-01-01', 'expiry_date' => '2027-01-01', 'issuing_country' => 'Saudi Arabia']);
        Passport::create(['customer_id' => $customer2->id, 'passport_number' => 'B87654321', 'full_name' => 'SARA AL-OTAIBI', 'first_name' => 'SARA', 'last_name' => 'AL-OTAIBI', 'date_of_birth' => '1995-08-20', 'nationality' => 'Saudi', 'gender' => 'female', 'issue_date' => '2023-06-15', 'expiry_date' => '2028-06-15', 'issuing_country' => 'Saudi Arabia']);

        // === 9. BOOKINGS ===
        $ecoSeat = FlightSeat::where('flight_id', $flightModels[0]->id)->where('class', 'economy')->first();
        $booking1 = Booking::create(['booking_reference' => 'SKB-' . strtoupper(Str::random(8)), 'customer_id' => $customer1->id, 'flight_id' => $flightModels[0]->id, 'flight_seat_id' => $ecoSeat->id, 'trip_type' => 'one_way', 'class' => 'economy', 'adults' => 1, 'total_price' => $ecoSeat->adult_price, 'commission_amount' => 15, 'net_price' => $ecoSeat->adult_price - 15, 'status' => 'confirmed', 'payment_status' => 'paid', 'provider_id' => $provider1->id, 'booked_by' => $cu1->id, 'confirmed_at' => now()]);
        BookingPassenger::create(['booking_id' => $booking1->id, 'first_name' => 'MOHAMMED', 'last_name' => 'AL-AHMAD', 'passport_number' => 'A12345678', 'date_of_birth' => '1990-05-15', 'gender' => 'male', 'nationality' => 'Saudi', 'type' => 'adult']);

        $bizSeat = FlightSeat::where('flight_id', $flightModels[2]->id)->where('class', 'business')->first();
        $booking2 = Booking::create(['booking_reference' => 'SKB-' . strtoupper(Str::random(8)), 'customer_id' => $customer2->id, 'flight_id' => $flightModels[2]->id, 'flight_seat_id' => $bizSeat->id, 'trip_type' => 'one_way', 'class' => 'business', 'adults' => 1, 'total_price' => $bizSeat->adult_price, 'commission_amount' => 15, 'net_price' => $bizSeat->adult_price - 15, 'status' => 'pending', 'payment_status' => 'pending', 'provider_id' => $provider1->id, 'booked_by' => $cu2->id]);
        BookingPassenger::create(['booking_id' => $booking2->id, 'first_name' => 'SARA', 'last_name' => 'AL-OTAIBI', 'passport_number' => 'B87654321', 'date_of_birth' => '1995-08-20', 'gender' => 'female', 'nationality' => 'Saudi', 'type' => 'adult']);

        // === 10. PAYMENT GATEWAYS ===
        PaymentGateway::create(['name_en' => 'Bank Transfer', 'name_ar' => 'تحويل بنكي', 'instructions_en' => 'Transfer to IBAN: SA1234567890. Send receipt.', 'instructions_ar' => 'حول إلى الآيبان: SA1234567890. أرسل الإيصال.', 'type' => 'manual', 'status' => 'active']);
        PaymentGateway::create(['name_en' => 'Visa / MasterCard', 'name_ar' => 'فيزا / ماستركارد', 'instructions_en' => 'Pay securely with your card.', 'instructions_ar' => 'ادفع بأمان ببطاقتك.', 'type' => 'automatic', 'status' => 'active']);
        PaymentGateway::create(['name_en' => 'STC Pay', 'name_ar' => 'STC Pay', 'instructions_en' => 'Pay using STC Pay wallet.', 'instructions_ar' => 'ادفع عبر محفظة STC Pay.', 'type' => 'manual', 'status' => 'active']);

        // === 11. FINANCIAL ACCOUNTS ===
        FinancialAccount::create(['account_name_en' => 'System Main Account', 'account_name_ar' => 'حساب النظام الرئيسي', 'account_type' => 'system', 'balance' => 10000, 'status' => 'active']);
        FinancialAccount::create(['account_name_en' => 'System Commissions', 'account_name_ar' => 'عمولات النظام', 'account_type' => 'commission', 'balance' => 500, 'status' => 'active']);
        FinancialAccount::create(['account_name_en' => 'Al-Safa Travel Account', 'account_name_ar' => 'حساب وكالة الصفا', 'account_type' => 'provider', 'provider_id' => $provider1->id, 'balance' => 5000, 'status' => 'active']);
        FinancialAccount::create(['account_name_en' => 'Gulf Star Account', 'account_name_ar' => 'حساب نجمة الخليج', 'account_type' => 'provider', 'provider_id' => $provider2->id, 'balance' => 3000, 'status' => 'active']);
        FinancialAccount::create(['account_name_en' => 'Quick Book Agent Account', 'account_name_ar' => 'حساب وكيل كويك بوك', 'account_type' => 'agent', 'agent_id' => $agent1->id, 'balance' => 2000, 'status' => 'active']);
        FinancialAccount::create(['account_name_en' => 'Travel Express Account', 'account_name_ar' => 'حساب ترافل إكسبرس', 'account_type' => 'agent', 'agent_id' => $agent2->id, 'balance' => 1500, 'status' => 'active']);

        // === 12. WHATSAPP GATEWAY ===
        WhatsappGateway::create(['name' => 'WAHA Gateway', 'api_url' => 'https://waha-api.example.com', 'api_key' => 'waha_key_placeholder', 'phone_number' => '+966500000000', 'status' => 'inactive']);

        // === 13. SETTINGS ===
        foreach ([
            ['key' => 'system_name', 'value' => 'SkyBooker', 'group' => 'general'],
            ['key' => 'system_name_ar', 'value' => 'سكاي بوكر', 'group' => 'general'],
            ['key' => 'default_locale', 'value' => 'ar', 'group' => 'general'],
            ['key' => 'currency', 'value' => 'USD', 'group' => 'general'],
            ['key' => 'timezone', 'value' => 'Asia/Riyadh', 'group' => 'general'],
            ['key' => 'primary_color', 'value' => '#7a1230', 'group' => 'appearance'],
            ['key' => 'accent_color', 'value' => '#d4a017', 'group' => 'appearance'],
            ['key' => 'booking_prefix', 'value' => 'SKB', 'group' => 'booking'],
        ] as $s) {
            Setting::create($s);
        }

        // === 14. EMPLOYEE ===
        User::create(['name' => 'Fatima Employee', 'email' => 'fatima@skybooker.com', 'password' => Hash::make('employee123'), 'username' => 'fatima', 'role' => 'employee', 'phone' => '966500000070', 'status' => 'active']);
    }
}
