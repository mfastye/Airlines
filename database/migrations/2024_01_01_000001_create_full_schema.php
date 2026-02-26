<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Users table - multi-role authentication
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('username')->unique();
            $table->enum('role', ['admin', 'provider', 'agent', 'employee', 'customer'])->default('customer');
            $table->string('phone')->nullable();
            $table->string('phone_country_code')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('whatsapp_country_code')->nullable();
            $table->string('avatar')->nullable();
            $table->string('google_id')->nullable();
            $table->enum('status', ['active', 'suspended', 'pending'])->default('active');
            $table->string('language', 5)->default('ar');
            $table->boolean('dark_mode')->default(false);
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->text('permissions')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // User activity logs
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action');
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Airlines
        Schema::create('airlines', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('code', 10)->unique();
            $table->string('iata_code', 5)->nullable();
            $table->string('logo')->nullable();
            $table->string('country_en')->nullable();
            $table->string('country_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        // Airports
        Schema::create('airports', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('code', 10)->unique();
            $table->string('iata_code', 5)->nullable();
            $table->string('city_en');
            $table->string('city_ar');
            $table->string('country_en');
            $table->string('country_ar');
            $table->string('timezone')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Providers (Travel agencies / Suppliers)
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('business_name_en');
            $table->string('business_name_ar');
            $table->string('contact_person');
            $table->string('phone');
            $table->string('phone_country_code')->default('+966');
            $table->string('whatsapp')->nullable();
            $table->string('whatsapp_country_code')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->enum('commission_type', ['fixed', 'percentage'])->default('fixed');
            $table->json('allowed_airlines')->nullable();
            $table->json('permissions')->nullable();
            $table->enum('status', ['active', 'suspended', 'pending'])->default('active');
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('currency', 5)->default('USD');
            $table->timestamps();
            $table->softDeletes();
        });

        // Agents (Sales agents under providers or system)
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('phone_country_code')->default('+966');
            $table->string('whatsapp')->nullable();
            $table->string('whatsapp_country_code')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('provider_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('currency', 5)->default('USD');
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->enum('commission_type', ['fixed', 'percentage'])->default('fixed');
            $table->enum('status', ['active', 'suspended', 'pending'])->default('active');
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->boolean('api_enabled')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // Flights
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_number');
            $table->foreignId('airline_id')->constrained()->onDelete('cascade');
            $table->foreignId('departure_airport_id')->constrained('airports')->onDelete('cascade');
            $table->foreignId('arrival_airport_id')->constrained('airports')->onDelete('cascade');
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time');
            $table->integer('duration_minutes')->nullable();
            $table->string('aircraft_type')->nullable();
            $table->integer('stops')->default(0);
            $table->text('stop_details')->nullable();
            $table->enum('status', ['scheduled', 'active', 'cancelled', 'completed', 'delayed'])->default('scheduled');
            $table->foreignId('provider_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Flight Seats / Pricing
        Schema::create('flight_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_id')->constrained()->onDelete('cascade');
            $table->enum('class', ['economy', 'business', 'first'])->default('economy');
            $table->integer('total_seats');
            $table->integer('available_seats');
            $table->decimal('adult_price', 10, 2);
            $table->decimal('child_price', 10, 2);
            $table->decimal('infant_price', 10, 2)->default(0);
            $table->integer('baggage_allowance_kg')->default(23);
            $table->integer('hand_baggage_kg')->default(7);
            $table->boolean('meal_included')->default(false);
            $table->boolean('wifi_available')->default(false);
            $table->boolean('entertainment_available')->default(false);
            $table->text('extra_services')->nullable();
            $table->foreignId('provider_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['available', 'sold_out', 'closed'])->default('available');
            $table->timestamps();
        });

        // Customers
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_country_code')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('whatsapp_country_code')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('nationality')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Passports
        Schema::create('passports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('passport_number');
            $table->string('full_name');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('issuing_country')->nullable();
            $table->string('passport_image')->nullable();
            $table->boolean('is_expired')->default(false);
            $table->json('ocr_data')->nullable();
            $table->timestamps();
        });

        // Bookings
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('flight_id')->constrained()->onDelete('cascade');
            $table->foreignId('flight_seat_id')->constrained()->onDelete('cascade');
            $table->foreignId('return_flight_id')->nullable()->constrained('flights')->onDelete('set null');
            $table->foreignId('return_flight_seat_id')->nullable()->constrained('flight_seats')->onDelete('set null');
            $table->enum('trip_type', ['one_way', 'round_trip'])->default('one_way');
            $table->enum('class', ['economy', 'business', 'first'])->default('economy');
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->integer('infants')->default(0);
            $table->decimal('total_price', 12, 2);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('net_price', 12, 2)->default(0);
            $table->string('currency', 5)->default('USD');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'refunded', 'no_show'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->boolean('has_visa')->default(false);
            $table->text('special_requests')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('provider_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('agent_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('booked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('ticket_number')->nullable();
            $table->string('ticket_image')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Booking Passengers
        Schema::create('booking_passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('passport_id')->nullable()->constrained()->onDelete('set null');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('passport_number')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('nationality')->nullable();
            $table->enum('type', ['adult', 'child', 'infant'])->default('adult');
            $table->string('seat_number')->nullable();
            $table->timestamps();
        });

        // Payment Gateways
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('logo')->nullable();
            $table->text('instructions_en')->nullable();
            $table->text('instructions_ar')->nullable();
            $table->enum('type', ['manual', 'automatic'])->default('manual');
            $table->string('api_endpoint')->nullable();
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->json('config')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->decimal('balance', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_gateway_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 5)->default('USD');
            $table->enum('method', ['wallet', 'visa', 'mastercard', 'bank_transfer', 'cash', 'other'])->default('other');
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'refunded'])->default('pending');
            $table->string('receipt_image')->nullable();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('confirmed_at')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Financial Accounts
        Schema::create('financial_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_name_en');
            $table->string('account_name_ar');
            $table->enum('account_type', ['system', 'provider', 'agent', 'gateway', 'commission']);
            $table->foreignId('provider_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('agent_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('payment_gateway_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('currency', 5)->default('USD');
            $table->enum('status', ['active', 'frozen', 'closed'])->default('active');
            $table->timestamps();
        });

        // Financial Transactions
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_account_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('currency', 5)->default('USD');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // WhatsApp Gateway
        Schema::create('whatsapp_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('api_url');
            $table->string('api_key')->nullable();
            $table->string('session_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->enum('status', ['active', 'inactive', 'connecting'])->default('inactive');
            $table->json('config')->nullable();
            $table->timestamps();
        });

        // WhatsApp Messages
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_gateway_id')->nullable()->constrained()->onDelete('set null');
            $table->string('recipient_phone');
            $table->string('recipient_name')->nullable();
            $table->text('message');
            $table->string('media_url')->nullable();
            $table->enum('type', ['text', 'image', 'document', 'template'])->default('text');
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed'])->default('pending');
            $table->enum('direction', ['outgoing', 'incoming'])->default('outgoing');
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->json('response_data')->nullable();
            $table->timestamps();
        });

        // Advertisements
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title_en')->nullable();
            $table->string('title_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('image');
            $table->string('url')->nullable();
            $table->enum('type', ['banner', 'whatsapp', 'popup'])->default('banner');
            $table->enum('position', ['home_top', 'home_bottom', 'sidebar'])->default('home_top');
            $table->integer('sort_order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('clicks')->default(0);
            $table->integer('views')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // System Settings
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text');
            $table->string('group')->default('general');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Employees
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->json('permissions')->nullable();
            $table->enum('status', ['active', 'suspended'])->default('active');
            $table->timestamps();
        });

        // Add foreign keys to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('set null');
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['provider_id']);
            $table->dropForeign(['agent_id']);
        });

        Schema::dropIfExists('employees');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('advertisements');
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_gateways');
        Schema::dropIfExists('financial_transactions');
        Schema::dropIfExists('financial_accounts');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_gateways');
        Schema::dropIfExists('booking_passengers');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('passports');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('flight_seats');
        Schema::dropIfExists('flights');
        Schema::dropIfExists('agents');
        Schema::dropIfExists('providers');
        Schema::dropIfExists('airports');
        Schema::dropIfExists('airlines');
        Schema::dropIfExists('user_activity_logs');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
