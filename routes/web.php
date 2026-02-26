<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\AirlineController;
use App\Http\Controllers\Admin\AirportController;
use App\Http\Controllers\Admin\FlightController as AdminFlightController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ProviderController;
use App\Http\Controllers\Admin\AgentController as AdminAgentController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\AdvertisementController;
use App\Http\Controllers\Admin\WhatsappController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Provider\DashboardController as ProviderDashboard;
use App\Http\Controllers\Provider\FlightController as ProviderFlightController;
use App\Http\Controllers\Provider\BookingController as ProviderBookingController;
use App\Http\Controllers\Provider\AgentController as ProviderAgentController;
use App\Http\Controllers\Agent\DashboardController as AgentDashboard;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/api/airports/search', [HomeController::class, 'searchAirports'])->name('airports.search');
Route::post('/language', [HomeController::class, 'setLanguage'])->name('language.set');
Route::post('/dark-mode', [HomeController::class, 'toggleDarkMode'])->name('dark-mode.toggle');
Route::get('/ad/{ad}/click', [HomeController::class, 'adClick'])->name('ad.click');

// Authentication
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/customer/login', [LoginController::class, 'showCustomerLogin'])->name('customer.login');
Route::post('/customer/register', [LoginController::class, 'customerRegister'])->name('customer.register');

// Customer Booking
Route::post('/booking/select-flight', [BookingController::class, 'selectFlight'])->name('booking.select-flight');
Route::middleware('auth')->group(function () {
    Route::get('/booking/passenger-details', [BookingController::class, 'passengerDetails'])->name('booking.passenger-details');
    Route::post('/booking/upload-passport', [BookingController::class, 'uploadPassport'])->name('booking.upload-passport');
    Route::post('/booking/store-passengers', [BookingController::class, 'storePassengerData'])->name('booking.store-passengers');
    Route::get('/booking/payment', [BookingController::class, 'payment'])->name('booking.payment');
    Route::post('/booking/process-payment', [BookingController::class, 'processPayment'])->name('booking.process-payment');
    Route::get('/booking/{booking}/confirmation', [BookingController::class, 'confirmation'])->name('booking.confirmation');
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('booking.my-bookings');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,employee'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::resource('airlines', AirlineController::class);
    Route::resource('airports', AirportController::class);
    Route::resource('flights', AdminFlightController::class);
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/confirm', [AdminBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/upload-ticket', [AdminBookingController::class, 'uploadTicket'])->name('bookings.upload-ticket');
    Route::resource('providers', ProviderController::class);
    Route::resource('agents', AdminAgentController::class);
    Route::post('/agents/{agent}/add-balance', [AdminAgentController::class, 'addBalance'])->name('agents.add-balance');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{payment}/confirm', [PaymentController::class, 'confirm'])->name('payments.confirm');
    Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
    Route::get('/payment-gateways', [PaymentController::class, 'gateways'])->name('payment-gateways.index');
    Route::get('/payment-gateways/create', [PaymentController::class, 'createGateway'])->name('payment-gateways.create');
    Route::post('/payment-gateways', [PaymentController::class, 'storeGateway'])->name('payment-gateways.store');
    Route::get('/payment-gateways/{gateway}/edit', [PaymentController::class, 'editGateway'])->name('payment-gateways.edit');
    Route::put('/payment-gateways/{gateway}', [PaymentController::class, 'updateGateway'])->name('payment-gateways.update');
    Route::resource('advertisements', AdvertisementController::class);
    Route::get('/whatsapp', [WhatsappController::class, 'index'])->name('whatsapp.index');
    Route::get('/whatsapp/gateway/create', [WhatsappController::class, 'createGateway'])->name('whatsapp.gateway.create');
    Route::post('/whatsapp/gateway', [WhatsappController::class, 'storeGateway'])->name('whatsapp.gateway.store');
    Route::get('/whatsapp/gateway/{gateway}/edit', [WhatsappController::class, 'editGateway'])->name('whatsapp.gateway.edit');
    Route::put('/whatsapp/gateway/{gateway}', [WhatsappController::class, 'updateGateway'])->name('whatsapp.gateway.update');
    Route::post('/whatsapp/gateway/{gateway}/toggle', [WhatsappController::class, 'toggleGateway'])->name('whatsapp.gateway.toggle');
    Route::post('/whatsapp/send', [WhatsappController::class, 'sendMessage'])->name('whatsapp.send');
    Route::get('/whatsapp/messages', [WhatsappController::class, 'messages'])->name('whatsapp.messages');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/backup', [SettingsController::class, 'backup'])->name('settings.backup');
    Route::get('/users', [SettingsController::class, 'users'])->name('users.index');
    Route::get('/users/{user}/log', [SettingsController::class, 'userActivityLog'])->name('users.log');
    Route::post('/users/{user}/toggle-status', [SettingsController::class, 'toggleUserStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/update-password', [SettingsController::class, 'updateUserPassword'])->name('users.update-password');
    Route::get('/customers', [SettingsController::class, 'customers'])->name('customers.index');
    Route::get('/financial', [SettingsController::class, 'financial'])->name('financial.index');
    Route::get('/financial/{account}/transactions', [SettingsController::class, 'financialTransactions'])->name('financial.transactions');
});

// Provider Routes
Route::prefix('provider')->name('provider.')->middleware(['auth', 'role:provider'])->group(function () {
    Route::get('/dashboard', [ProviderDashboard::class, 'index'])->name('dashboard');
    Route::resource('flights', ProviderFlightController::class);
    Route::get('/bookings', [ProviderBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [ProviderBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/confirm', [ProviderBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/upload-ticket', [ProviderBookingController::class, 'uploadTicket'])->name('bookings.upload-ticket');
    Route::get('/agents', [ProviderAgentController::class, 'index'])->name('agents.index');
    Route::get('/agents/create', [ProviderAgentController::class, 'create'])->name('agents.create');
    Route::post('/agents', [ProviderAgentController::class, 'store'])->name('agents.store');
    Route::get('/agents/{agent}', [ProviderAgentController::class, 'show'])->name('agents.show');
    Route::post('/agents/{agent}/add-balance', [ProviderAgentController::class, 'addBalance'])->name('agents.add-balance');
    Route::post('/agents/{agent}/toggle-status', [ProviderAgentController::class, 'toggleStatus'])->name('agents.toggle-status');
});

// Agent Routes
Route::prefix('agent')->name('agent.')->middleware(['auth', 'role:agent'])->group(function () {
    Route::get('/dashboard', [AgentDashboard::class, 'index'])->name('dashboard');
    Route::post('/search', [AgentDashboard::class, 'search'])->name('search');
    Route::post('/book', [AgentDashboard::class, 'book'])->name('book');
    Route::get('/bookings', [AgentDashboard::class, 'bookings'])->name('bookings');
    Route::get('/booking/{booking}', [AgentDashboard::class, 'showBooking'])->name('booking.show');
    Route::get('/financial', [AgentDashboard::class, 'financial'])->name('financial');
});
