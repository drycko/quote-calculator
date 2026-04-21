<?php

use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\LineItemController;
use App\Http\Controllers\LineItemTemplateController;
use App\Http\Controllers\QuoteController;
use Illuminate\Support\Facades\Route;

// Welcome — always public landing
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// ── Public calculator (no auth) ──────────────────────────────
Route::get('/calculator',                                   [CalculatorController::class, 'create'])->name('calculator.create');
Route::post('/calculator',                                  [CalculatorController::class, 'store'])->name('calculator.store');
Route::get('/calculator/{token}',                           [CalculatorController::class, 'show'])->name('calculator.show');
Route::put('/calculator/{token}',                           [CalculatorController::class, 'update'])->name('calculator.update');
Route::post('/calculator/{token}/recalculate',              [CalculatorController::class, 'recalculate'])->name('calculator.recalculate');
Route::get('/calculator/{token}/pdf',                       [CalculatorController::class, 'downloadPdf'])->name('calculator.pdf');
Route::post('/calculator/{token}/items',                    [CalculatorController::class, 'addItem'])->name('calculator.items.store');
Route::delete('/calculator/{token}/items/{lineItem}',       [CalculatorController::class, 'removeItem'])->name('calculator.items.destroy');

Auth::routes();

// ── Admin management (auth required) ───────────────────────
Route::middleware(['auth'])->group(function () {
    // /home → redirect to admin quotes
    Route::get('/home', function () {
        return redirect()->route('quotes.index');
    })->name('home');

    Route::resource('quotes', QuoteController::class);

    Route::post('/quotes/{quote}/recalculate', [QuoteController::class, 'recalculate'])
        ->name('quotes.recalculate');

    Route::get('/quotes/{quote}/pdf', [QuoteController::class, 'downloadPdf'])
        ->name('quotes.pdf');

    // Admin line item management (for admin edit view)
    Route::post('/line-items', [LineItemController::class, 'store'])->name('line-items.store');
    Route::delete('/line-items/{lineItem}', [LineItemController::class, 'destroy'])->name('line-items.destroy');

    // Line item templates
    Route::resource('templates', LineItemTemplateController::class)->except(['show']);
});
