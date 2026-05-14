<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Manager;

// Landing page (public)
Route::get('/', fn() => view('landing'))->name('landing');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Profile (any authenticated user)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('managers', Admin\ManagerController::class)->except(['show']);
    Route::post('managers/{manager}/impersonate', [Admin\ManagerController::class, 'impersonate'])
        ->name('managers.impersonate');
});

// Manager
Route::prefix('manager')->name('manager.')->middleware(['auth', 'manager'])->group(function () {

    // Company context (selection / switch) — does NOT require current-company middleware
    Route::get('/company-select', [Manager\CompanyContextController::class, 'select'])->name('company-select');
    Route::post('/company-switch/{company}', [Manager\CompanyContextController::class, 'switch'])->name('company-switch');

    // Companies management (does NOT require current company)
    Route::resource('companies', Manager\CompanyController::class)->except(['show']);

    // All routes below require an active company in session
    Route::middleware('current-company')->group(function () {

        Route::get('/dashboard', [Manager\DashboardController::class, 'index'])->name('dashboard');

        // Clients
        Route::resource('clients', Manager\ClientController::class)->except(['show']);

        // Get clients list (AJAX)
        Route::get('clients-list', function () {
            $company = app('currentCompany');
            return response()->json(
                $company->clients()->orderBy('company_name')->get(['id', 'company_name', 'address', 'phone', 'ice'])
            );
        })->name('clients-list');

        // Autocomplete prestations
        Route::get('autocomplete', [Manager\ServiceDescriptionController::class, 'autocomplete'])
            ->name('autocomplete');

        // Quotes
        Route::resource('quotes', Manager\QuoteController::class);
        Route::get('quotes/{quote}/pdf', [Manager\QuoteController::class, 'pdf'])->name('quotes.pdf');
        Route::get('quotes/{quote}/convert-to-invoice', [Manager\QuoteController::class, 'convertToInvoice'])
            ->name('quotes.convert-to-invoice');
        Route::patch('quotes/{quote}/status', [Manager\QuoteController::class, 'updateStatus'])->name('quotes.update-status');

        // Invoices
        Route::resource('invoices', Manager\InvoiceController::class);
        Route::get('invoices/{invoice}/pdf', [Manager\InvoiceController::class, 'pdf'])->name('invoices.pdf');
        Route::post('invoices/from-source', [Manager\InvoiceController::class, 'storeFromSource'])
            ->name('invoices.from-source');
        Route::patch('invoices/{invoice}/status', [Manager\InvoiceController::class, 'updateStatus'])->name('invoices.update-status');

        // Invoice payments
        Route::post('invoices/{invoice}/payments', [Manager\InvoicePaymentController::class, 'store'])
            ->name('invoice-payments.store');
        Route::patch('invoices/{invoice}/payments/{payment}', [Manager\InvoicePaymentController::class, 'update'])
            ->name('invoice-payments.update');
        Route::delete('invoices/{invoice}/payments/{payment}', [Manager\InvoicePaymentController::class, 'destroy'])
            ->name('invoice-payments.destroy');

        // Delivery Notes
        Route::resource('delivery-notes', Manager\DeliveryNoteController::class);
        Route::get('delivery-notes/{deliveryNote}/pdf', [Manager\DeliveryNoteController::class, 'pdf'])
            ->name('delivery-notes.pdf');
        Route::patch('delivery-notes/{deliveryNote}/status', [Manager\DeliveryNoteController::class, 'updateStatus'])->name('delivery-notes.update-status');

        // Markets
        Route::resource('markets', Manager\MarketController::class);
        Route::patch('markets/{market}/status', [Manager\MarketController::class, 'updateStatus'])->name('markets.update-status');
        Route::get('markets-export', [Manager\MarketController::class, 'export'])->name('markets.export');
        Route::get('markets/{market}/attachments/{attachment}/download', [Manager\MarketController::class, 'downloadAttachment'])->name('market-attachments.download');
        Route::delete('markets/{market}/attachments/{attachment}', [Manager\MarketController::class, 'destroyAttachment'])->name('market-attachments.destroy');

        // Purchase Orders
        Route::resource('purchase-orders', Manager\PurchaseOrderController::class);
        Route::get('purchase-orders/{purchaseOrder}/pdf', [Manager\PurchaseOrderController::class, 'pdf'])
            ->name('purchase-orders.pdf');
        Route::get('purchase-orders/{purchaseOrder}/convert-to-invoice', [Manager\PurchaseOrderController::class, 'convertToInvoice'])
            ->name('purchase-orders.convert-to-invoice');
        Route::patch('purchase-orders/{purchaseOrder}/status', [Manager\PurchaseOrderController::class, 'updateStatus'])->name('purchase-orders.update-status');
    });
});
