<?php

use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\LineItemController;
use App\Http\Controllers\LineItemTemplateController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\UserController;
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
Route::post('/calculator/{token}/submit',                   [CalculatorController::class, 'submit'])->name('calculator.submit');
Route::post('/calculator/{token}/approve',                  [CalculatorController::class, 'approve'])->name('calculator.approve');
Route::get('/calculator/{token}/pdf',                       [CalculatorController::class, 'downloadPdf'])->name('calculator.pdf');
Route::post('/calculator/{token}/items',                    [CalculatorController::class, 'addItem'])->name('calculator.items.store');
Route::delete('/calculator/{token}/items/{lineItem}',       [CalculatorController::class, 'removeItem'])->name('calculator.items.destroy');
Route::patch('/calculator/{token}/items/{lineItem}/move',   [CalculatorController::class, 'moveItem'])->name('calculator.items.move');
Route::patch('/calculator/{token}/items/{lineItem}/qty',    [CalculatorController::class, 'updateItemQty'])->name('calculator.items.qty');

Auth::routes();

// create admin to redirect to home
Route::get('/admin', function () {
    return redirect()->route('quotes.index');
})->name('home');

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

    Route::post('/quotes/{quote}/email', [QuoteController::class, 'sendEmail'])
        ->name('quotes.email');

    // Admin line item management (for admin edit view)
    Route::post('/line-items', [LineItemController::class, 'store'])->name('line-items.store');
    Route::put('/line-items/{lineItem}', [LineItemController::class, 'update'])->name('line-items.update');
    Route::delete('/line-items/{lineItem}', [LineItemController::class, 'destroy'])->name('line-items.destroy');
    Route::patch('/line-items/{lineItem}/move', [LineItemController::class, 'move'])->name('line-items.move');

    // Line item templates
    Route::resource('templates', LineItemTemplateController::class)->except(['show']);

    // Users
    Route::resource('users', UserController::class)->except(['show']);
});
