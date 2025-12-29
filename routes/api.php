<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\API\SizeController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\API\DesignController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\DesignOptionController;
use App\Http\Controllers\Api\Admin\AdminOrderController;
use App\Http\Controllers\API\WalletController;
use App\Http\Controllers\API\PaymentController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


// قائمة المدن المتاحة
Route::get('cities', [LocationController::class, 'getCities'])
     ->name('cities.index');

// قائمة المناطق في مدينة
Route::get('cities/{city}/areas', [LocationController::class, 'getAreas'])
     ->name('cities.areas');

// المقاسات المتاحة (للعرض العام)
Route::get('sizes', [SizeController::class, 'index'])->name('sizes.index');
Route::get('sizes/{size}', [SizeController::class, 'show'])->name('sizes.show');

// أنواع خيارات التصميم
Route::get('design-options/types', [DesignOptionController::class, 'types'])
     ->name('design-options.types');

// -----------------------------------------------------------------
// Payment Callback Routes (Public - للعودة من Stripe)
// -----------------------------------------------------------------
Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/cancel', [PaymentController::class, 'cancel'])->name('cancel');
    Route::get('/order-success', [PaymentController::class, 'orderSuccess'])->name('order-success');
    Route::get('/order-cancel', [PaymentController::class, 'orderCancel'])->name('order-cancel');
});

// =================================================================
// Protected Routes (تحتاج تسجيل دخول)
// =================================================================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // -----------------------------------------------------------------
    // User Profile Routes
    // -----------------------------------------------------------------
    Route::get('user/me', [\App\Http\Controllers\API\UserController::class, 'me'])->name('user.me');
    Route::put('user/me', [\App\Http\Controllers\API\UserController::class, 'updateMe'])->name('user.update-me');
    Route::patch('user/me', [\App\Http\Controllers\API\UserController::class, 'updateMe'])->name('user.update-me');

    Route::apiResource('users', \App\Http\Controllers\API\UserController::class)->except(['store']);

    // Location Routes - الروابط المخصصة يجب أن تكون قبل apiResource
    Route::get('locations/default/get', [LocationController::class, 'getDefaultLocation'])
         ->name('locations.default');
    Route::get('my-locations/stats', [LocationController::class, 'myStats'])
         ->name('locations.my-stats');
    Route::post('locations/{location}/set-default', [LocationController::class, 'setAsDefault'])
         ->name('locations.set-default');
    Route::apiResource('locations', LocationController::class);

    // -----------------------------------------------------------------
    // Design Options Routes (خيارات التصميم)
    // العرض: للجميع | الإنشاء/التعديل/الحذف: للأدمن فقط
    // -----------------------------------------------------------------
    Route::get('design-options/grouped', [DesignOptionController::class, 'grouped'])
         ->name('design-options.grouped');
    Route::get('design-options/stats', [DesignOptionController::class, 'stats'])
         ->name('design-options.stats');
    Route::apiResource('design-options', DesignOptionController::class);

    // -----------------------------------------------------------------
    // Design Routes (التصاميم)
    // -----------------------------------------------------------------
    
    // روابط خاصة للمستخدمين
    Route::get('designs/my-designs', [DesignController::class, 'myDesigns'])
         ->name('designs.my-designs');
    Route::get('designs/browse', [DesignController::class, 'browse'])
         ->name('designs.browse');
    Route::get('designs/stats', [DesignController::class, 'stats'])
         ->name('designs.stats');
    
    // روابط خاصة للأدمن
    Route::get('admin/designs', [DesignController::class, 'adminIndex'])
         ->name('admin.designs.index');
    
    // CRUD عادي للتصاميم
    Route::apiResource('designs', DesignController::class);
    
    // -----------------------------------------------------------------
    // Cart Routes (السلة)
    // -----------------------------------------------------------------
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/', [CartController::class, 'store'])->name('store');
        Route::put('/{cartKey}', [CartController::class, 'update'])->name('update');
        Route::delete('/{cartKey}', [CartController::class, 'destroy'])->name('destroy');
        Route::post('/clear', [CartController::class, 'clear'])->name('clear');
    });
    
    // -----------------------------------------------------------------
    // User Order Routes (طلبات المستخدم)
    // -----------------------------------------------------------------
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    });
    
    // -----------------------------------------------------------------
    // Admin Order Routes (إدارة الطلبات للأدمن)
    // -----------------------------------------------------------------
    Route::prefix('admin/orders')->name('admin.orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::get('/statistics', [AdminOrderController::class, 'statistics'])->name('statistics');
        Route::get('/export', [AdminOrderController::class, 'export'])->name('export');
        Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
        Route::patch('/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{order}', [AdminOrderController::class, 'destroy'])->name('destroy');
    });
    
    // -----------------------------------------------------------------
    // Wallet Routes (المحفظة الرقمية)
    // -----------------------------------------------------------------
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('index');
        Route::get('/transactions', [WalletController::class, 'transactions'])->name('transactions');
        Route::post('/deposit', [WalletController::class, 'deposit'])->name('deposit'); // Admin only
        Route::post('/withdraw', [WalletController::class, 'withdraw'])->name('withdraw'); // Admin only
    });
    
    // -----------------------------------------------------------------
    // Payment Routes (الدفع عبر Stripe)
    // -----------------------------------------------------------------
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::post('/create-checkout-session', [PaymentController::class, 'createCheckoutSession'])->name('create-checkout-session');
        Route::get('/check-session/{sessionId}', [PaymentController::class, 'checkSession'])->name('check-session');
    });
    
    // -----------------------------------------------------------------
    // Order Payment Routes (دفع الطلبات - للمستخدمين فقط)
    // -----------------------------------------------------------------
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::post('/{order}/pay', [PaymentController::class, 'payOrder'])->name('pay');
    });
});