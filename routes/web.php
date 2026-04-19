<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Static content pages
Route::view('/about', 'about.index')->name('about');
Route::view('/terms', 'terms')->name('terms');
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/daily-offers', 'daily-offers')->name('daily-offers');

// Menu
Route::prefix('menu')->name('menu.')->group(function () {
    Route::get('/', [MenuController::class, 'index'])->name('index');
    Route::get('/drinks', [MenuController::class, 'drinks'])->name('drinks');
    Route::get('/category/{category:slug}', [MenuController::class, 'category'])->name('category');
    Route::get('/item/{item:slug}', [MenuController::class, 'show'])->name('item');
});
Route::redirect('/drinks', '/menu/drinks');

// Cart
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/remove', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
});

// Checkout
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    Route::get('/success', [CheckoutController::class, 'success'])->name('success');
    Route::post('/create-payment-intent', [CheckoutController::class, 'createPaymentIntent'])->name('payment-intent');
});

// Gift Cards — controller not yet implemented; routes removed to avoid 500s.

// Orders
Route::get('/orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');

// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact/submit', [ContactController::class, 'submit'])->name('contact.submit');

// Events
Route::get('/events', [EventsController::class, 'index'])->name('events');
Route::post('/events/inquiry', [EventsController::class, 'inquiry'])->name('events.inquiry');

// Language switcher
Route::get('/language/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'fr'], true)) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('language.switch');

// Stripe webhook — CSRF-exempt (see bootstrap/app.php)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

// Admin CSV export — requires auth + admin
Route::get('/admin/orders/export', [OrderController::class, 'exportCsv'])
    ->middleware(['auth', \App\Http\Middleware\EnsureUserIsAdmin::class])
    ->name('admin.orders.export');

// Authenticated profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
