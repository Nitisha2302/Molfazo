<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Vendor\StoreController;
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\OrderController;
use App\Http\Controllers\Vendor\ChatController;
use App\Http\Controllers\Vendor\CategoryController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

 // throttle.auth used for multiple attempts

Route::middleware('auth:sanctum', 'throttle.auth')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::post('vendor/register', [AuthController::class, 'vendorRegister']);
// Route::post('send-otp', [AuthController::class, 'sendOtp']);
// Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
// Route::post('resend-otp', [AuthController::class, 'resendOtp']);

Route::post('otp/mobile/send', [AuthController::class, 'sendMobileOtp']);
Route::post('otp/email/send', [AuthController::class, 'sendEmailOtp']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('vendor/register', [AuthController::class, 'vendorRegister']);
Route::post('vendor/login/otp/send',   [AuthController::class, 'sendVendorLoginOtp']);
Route::post('vendor/login/otp/verify', [AuthController::class, 'verifyLoginOtp']);

Route::post('vendor/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('vendor/reset-forgot-password', [AuthController::class, 'resetForgotPassword']);





 Route::post('vendor/login', [AuthController::class, 'vendorLogin']);
 Route::get('get-profile', [AuthController::class, 'getProfile']);
 Route::post('/logout', [AuthController::class, 'logout']);


    Route::get('vendor/categories', [CategoryController::class, 'categories']); // All categories with subcategories
    Route::get('vendor/subcategories/{category_id}', [CategoryController::class, 'subcategories']); // Subcategories by category


    // STORES
    Route::post('vendor/store/create', [StoreController::class, 'create']);
    Route::get('vendor/store/list', [StoreController::class, 'list']);
    Route::get('vendor/store/{id}', [StoreController::class, 'details']);

    // PRODUCTS
     Route::post('vendor/product/create', [ProductController::class, 'create']);
    Route::get('vendor/product/list', [ProductController::class, 'list']);
    Route::get('vendor/product/details/{id}', [ProductController::class, 'details']);

    // ORDERS
    Route::get('vendor/orders', [OrderController::class, 'list']);
    Route::post('vendor/order/accept/{id}', [OrderController::class, 'accept']);
    Route::post('vendor/order/complete/{id}', [OrderController::class, 'complete']);

    // CHAT
    Route::get('vendor/chats', [ChatController::class, 'list']);
    Route::post('vendor/chat/send', [ChatController::class, 'send']);
