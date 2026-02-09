<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\DashboardController;
use App\Livewire\CreateIncident;
use App\Livewire\CreateProduct;
use App\Livewire\EditIncident;
use App\Livewire\PortalDashboard;
use App\Livewire\PublicDashboard;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', PortalDashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/public', PublicDashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('public.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 製品ルート（createはLivewire、その他はController）
    Route::get('/products/create', CreateProduct::class)->name('products.create');
    Route::resource('products', ProductController::class)->except(['create']);
    
    Route::get('/incidents', [IncidentController::class, 'index'])->name('incidents.index');
    Route::get('/incidents/create', CreateIncident::class)->name('incidents.create');
    Route::get('/incidents/{incident}/edit', EditIncident::class)->name('incidents.edit');
    Route::resource('products.incidents', IncidentController::class)->scoped();

    Route::get('/mode/switch/{mode}', function ($mode) {
        if (in_array($mode, ['private', 'public'])) {
            session(['view_mode' => $mode]);
        }
        return back();
    })->name('mode.switch');
});

require __DIR__.'/auth.php';

Route::get('/check-locale', function () {
    return 'Current locale: ' . app()->getLocale();
});

Route::get('/api-debug', function () {
    abort_unless(app()->environment('local'), 404);

    $id = (string) config('services.rakuten.app_id');
    $response = Http::timeout(5)->get('https://app.rakuten.co.jp/services/api/Product/Search/20170426', [
        'applicationId' => $id,
        'keyword' => 'sony',
        'format' => 'json',
    ]);

    return $response->json();
});
