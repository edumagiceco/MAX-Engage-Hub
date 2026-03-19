<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\InboxController;
use App\Http\Controllers\Admin\CaseLibraryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicLeadController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicLeadController::class, 'home'])->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('forms')->name('forms.')->group(function (): void {
    Route::get('/contact', [PublicLeadController::class, 'show'])->defaults('form', 'contact')->name('contact');
    Route::post('/contact', [PublicLeadController::class, 'store'])->defaults('form', 'contact')->name('contact.store');

    Route::get('/education', [PublicLeadController::class, 'show'])->defaults('form', 'education')->name('education');
    Route::post('/education', [PublicLeadController::class, 'store'])->defaults('form', 'education')->name('education.store');

    Route::get('/diagnosis', [PublicLeadController::class, 'show'])->defaults('form', 'diagnosis')->name('diagnosis');
    Route::post('/diagnosis', [PublicLeadController::class, 'store'])->defaults('form', 'diagnosis')->name('diagnosis.store');

    Route::get('/recommendation', [PublicLeadController::class, 'show'])->defaults('form', 'recommendation')->name('recommendation');
    Route::post('/recommendation', [PublicLeadController::class, 'store'])->defaults('form', 'recommendation')->name('recommendation.store');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', fn () => redirect()->route('admin.inbox'));
    Route::get('/inbox', [InboxController::class, 'index'])->name('inbox');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::patch('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::post('/customers/{customer}/notes', [CustomerController::class, 'storeNote'])->name('customers.notes.store');
    Route::post('/customers/{customer}/tasks', [CustomerController::class, 'storeTask'])->name('customers.tasks.store');
    Route::post('/customers/{customer}/tags', [CustomerController::class, 'storeTag'])->name('customers.tags.store');
    Route::delete('/customers/{customer}/tags/{tag}', [CustomerController::class, 'destroyTag'])->name('customers.tags.destroy');

    Route::get('/cases', [CaseLibraryController::class, 'index'])->name('cases.index');
    Route::post('/cases', [CaseLibraryController::class, 'store'])->name('cases.store');
});
