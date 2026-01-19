<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('mentions-legales', 'pages.mentions-legales')->name('mentions-legales');
Route::view('confidentialite', 'pages.confidentialite')->name('confidentialite');
Route::view('plan-du-site', 'pages.plan-du-site')->name('plan-du-site');
Route::view('plan-festa', 'pages.planfeste')->name('plan-festa');

// Route temporaire pour prévisualiser le mail de contact
Route::get('/preview-mail', function () {
    if (!app()->isLocal()) {
        abort(404);
    }

    $type = request('type', 'info');
    
    $data = match($type) {
        'press' => [
            'name' => 'Jean Presse',
            'email' => 'journaliste@media.com',
            'subject' => 'Presse & Média',
            'message' => 'Demande d\'interview concernant les festivités du vendredi soir.'
        ],
        'part' => [
            'name' => 'Société Partenaire',
            'email' => 'directeur@entreprise.fr',
            'subject' => 'Partenariat',
            'message' => 'Nous souhaiterions sponsoriser le feu d\'artifice de clôture.'
        ],
        default => [
            'name' => 'Marie Curieuse',
            'email' => 'marie@gmail.com',
            'subject' => 'Informations Générales',
            'message' => 'Pouvez-vous me confirmer les horaires de la parade ?'
        ],
    };

    return new \App\Mail\ContactFormMail($data);
});

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['fr', 'ca'])) {
        session()->put('locale', $locale);
    }

    return back();
})->name('lang.switch');

Volt::route('boutique', 'boutique')->name('boutique');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/members', function () {
        return view('admin.members');
    })->name('members');

    Route::get('/products', function () {
        return view('admin.products');
    })->name('products');

    Route::get('/reservations', function () {
        return view('admin.reservations');
    })->name('reservations');

    Route::get('/program', function () {
        return view('admin.program');
    })->name('program');

    Route::get('/gallery', function () {
        return view('admin.gallery');
    })->name('gallery');

    Route::get('/flyers', function () {
        return view('admin.flyers');
    })->name('flyers');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::view('dashboard', 'dashboard')
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    Route::delete('orders/{order}', function (\App\Models\Order $order) {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        $order->delete();

        return back()->with('status', 'Commande annulée.');
    })->name('orders.destroy');

    Route::get('orders/{order}/invoice', [\App\Http\Controllers\InvoiceController::class, 'download'])->name('orders.invoice');
});

// Surcharge de la route d'inscription pour appliquer le Rate Limiting (3/heure)
Route::post('/register', [\Laravel\Fortify\Http\Controllers\RegisteredUserController::class, 'store'])
    ->middleware(['guest', 'throttle:register'])
    ->name('register');

require __DIR__.'/settings.php';