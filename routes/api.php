<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\vendor\StoreController;
use App\Http\Controllers\vendor\ProductController;
use App\Http\Controllers\vendor\OrderController;
use App\Http\Controllers\vendor\ChatController;
use App\Http\Controllers\vendor\CategoryController;




use App\Http\Controllers\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\ProductReviewController;

use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\Customer\StoreController as CustomerStoreController;
use App\Http\Controllers\Customer\CategoryController as CustomerCategoryController;
use App\Http\Controllers\vendor\VendorBankController;

use App\Http\Controllers\vendor\NotificationController;
use App\Http\Controllers\vendor\PromotionController;
use App\Http\Controllers\vendor\ReviewController;
use App\Http\Controllers\vendor\KycController;
use App\Http\Controllers\ContentController;

use App\Http\Controllers\Customer\ChatController as CustomerChatController;

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

Route::post('update-language', [AuthController::class, 'updateLanguage']);
Route::get('get-language', [AuthController::class, 'getLanguage']);

Route::get('privacy-policy', [ContentController::class, 'privacyPolicy']);
Route::get('terms-conditions', [ContentController::class, 'termsConditions']);

 Route::post('enquiry/store', [ContentController::class, 'storeEnquiry']);
Route::get('enquiry/list', [ContentController::class, 'myEnquiries']);

// Route::post('vendor/register', [AuthController::class, 'vendorRegister']);
// Route::post('send-otp', [AuthController::class, 'sendOtp']);
// Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
// Route::post('resend-otp', [AuthController::class, 'resendOtp']);

Route::post('otp/mobile/send', [AuthController::class, 'sendMobileOtp']);
Route::post('otp/email/send', [AuthController::class, 'sendEmailOtp']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('vendor/complete-profile', [AuthController::class, 'vendorCompleteProfile']);
Route::post('vendor/login/otp/send',   [AuthController::class, 'sendVendorLoginOtp']);
Route::post('vendor/login/otp/verify', [AuthController::class, 'verifyLoginOtp']);

Route::post('vendor/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('vendor/reset-forgot-password', [AuthController::class, 'resetForgotPassword']);


 Route::post('vendor/login/apple', [AuthController::class, 'VendorloginWithApple']);



 Route::post('vendor/login', [AuthController::class, 'vendorLogin']);
 Route::get('get-profile', [AuthController::class, 'getProfile']);
 Route::post('/logout', [AuthController::class, 'logout']);


    Route::get('vendor/categories', [CategoryController::class, 'categories']); // All categories with subcategories
    Route::get('vendor/subcategories/{category_id}', [CategoryController::class, 'subcategories']); // Subcategories by category
    Route::get('vendor/child-categories/{sub_category_id}',[CategoryController::class, 'childCategories']);


    Route::get(
        'vendor/attributes/{child_category_id}',
        [CategoryController::class, 'getAttributeByChildCategory']
    );

    // STORES
    Route::post('vendor/store/create', [StoreController::class, 'create']);
     Route::post('vendor/store/edit/{id}', [StoreController::class, 'update']);
    Route::get('vendor/store/list', [StoreController::class, 'list']);
    Route::get('vendor/store/details/{id}', [StoreController::class, 'details']);
    

    Route::get('banners', [CategoryController::class, 'getBanners']);
    Route::get('cities', [CategoryController::class, 'getCities']);
    Route::get('vendor/banks', [ProductController::class, 'getBankList']);
    
    Route::post('vendor/payment/save', [VendorBankController::class, 'saveVendorPayment']);
    Route::get('vendor/payment/details', [VendorBankController::class, 'getVendorPayment']);


    // PRODUCTS
     Route::post('vendor/product/create', [ProductController::class, 'create']);
        Route::post('vendor/product/analyzeImage', [ProductController::class, 'analyzeImage']);
     
   Route::post('vendor/product/edit/{id}', [ProductController::class, 'update']);
    Route::get('vendor/product/list', [ProductController::class, 'list']);
    Route::get('vendor/product/details/{id}', [ProductController::class, 'details']);

    Route::post('vendor/product/check-name', [ProductController::class, 'checkProductName']);

    Route::get('vendor/store/{store_id}/products', [ProductController::class, 'getstoreAllProducts']);

     Route::get('/vendor/dashboard', [ProductController::class, 'dashboard']);


    // ORDERS
     Route::get('vendor/orders', action: [OrderController::class, 'list']);
    Route::post('vendor/orders/{id}/status', [OrderController::class, 'updateStatus']);

    // CHAT
    // Route::get('vendor/chats', [ChatController::class, 'list']);
    // Route::post('vendor/chat/send', [ChatController::class, 'send']);

    Route::get('vendor/rejections', [NotificationController::class, 'getRejections']);


    Route::get('vendor/packages', [PromotionController::class,'packages']);
    Route::get('vendor/payment-details', [PromotionController::class,'paymentDetails']);
    Route::post('vendor/promotion-request', [PromotionController::class,'store']);

    Route::post('vendor/add-review', [ReviewController::class,'store']);

    Route::get('vendor/store/video-plans', [StoreController::class, 'plans']);
    Route::post('vendor/store/video-request', [StoreController::class, 'sendVideoRequest']);
     Route::post('vendor/store/video-upload', [StoreController::class, 'uploadStoreVideo']);

// CUSTOMER APIs


Route::post('customer/login', [CustomerAuthController::class, 'login']);
Route::post('customer/verify-otp', [CustomerAuthController::class, 'verifyOtp']);
Route::post('customer/update-profile', [CustomerAuthController::class, 'updateProfile']);

Route::post('customer/address/save', [CustomerAuthController::class, 'storeAddress']);
Route::get('customer/address/list', [CustomerAuthController::class, 'addressList']);
Route::delete('customer/address/remove/{id}', action: [CustomerAuthController::class, 'destroyAddress']);
Route::post('customer/address/set-default', [CustomerAuthController::class, 'setDefaultAddress']);
Route::post('delete-account', [CustomerAuthController::class, 'deleteAccount']);


Route::get('customer/categories', [CustomerCategoryController::class, 'categories']); // All categories with sub & child
Route::get('customer/category/{id}/subcategories', [CustomerCategoryController::class, 'subCategories']); // Subcategories only
Route::get('customer/subcategory/{id}/childcategories', [CustomerCategoryController::class, 'childCategories']); // Child categories only


// PRODUCTS (CUSTOMER VIEW)
Route::get('customer/products', [CustomerProductController::class, 'list']);
Route::get('customer/product/{id}', [CustomerProductController::class, 'details']);
Route::post('customer/product/favorite/toggle', [CustomerProductController::class,'toggleFavorite']);
Route::get('customer/product/favorite/list', [CustomerProductController::class,'favoriteList']);



// stores (CUSTOMER VIEW)
Route::get('customer/stores', [CustomerStoreController::class, 'list']);
Route::get('customer/store/{id}', [CustomerStoreController::class, 'details']);



// CART
 Route::post('customer/cart/add', [CartController::class, 'add']);
Route::get('customer/cart/list', [CartController::class, 'list']);
Route::post('customer/cart/update', [CartController::class, 'update']);
Route::delete('customer/cart/remove/{id}', [CartController::class, 'remove']);

// ORDER
Route::get('customer/available-banks', [CustomerOrderController::class, 'availableBanks']);
 Route::post('customer/order/place', [CustomerOrderController::class, 'placeOrder']);
 Route::get('customer/orders', [CustomerOrderController::class, 'myOrders']);
 Route::get('customer/order/{id}', [CustomerOrderController::class, 'orderDetails']);

 Route::get('customer/products/search', [CustomerProductController::class, 'search']);
 Route::get('/global-search', [CustomerProductController::class, 'globalSearch']);



// CUSTOMER CHAT
// CUSTOMER CHAT
Route::post('customer/chat/start', [CustomerChatController::class, 'start']);
Route::get('customer/chat/conversations', [CustomerChatController::class, 'allConversation']);
Route::post('customer/chat/messages', [CustomerChatController::class, 'allMessages']);
Route::post('customer/chat/send', [CustomerChatController::class, 'send']);
Route::post('customer/chat/mark-read', [CustomerChatController::class, 'markRead']);



  Route::post('customer/product/review/store', [ProductReviewController::class, 'store']);
  Route::get('customer/product/{id}/reviews', [ProductReviewController::class, 'list']);

  //new flow

Route::get('category-attributes/{child_category_id}',[ProductController::class,'getCategoryAttributes']);

Route::post('vendor/product/combination/update/{id}',[ProductController::class,'updateCombination']);

Route::delete('vendor/product/combination/delete/{id}',[ProductController::class,'deleteCombination']);

Route::post('vendor/product/copy/{id}',[ProductController::class, 'copyProduct']);

// ================= KYC =================

// Protected
Route::middleware('auth:api')->group(function () {
    Route::post('/kyc/create-session', [KycController::class, 'createSession']);
});


// Public (DIDIT webhook)
Route::post('/didit/webhook', [KycController::class, 'webhook'])->name('didit.webhook');

Route::post('toggle-block-user', [CustomerAuthController::class, 'toggleBlockUser']);
  Route::post('/store-report', [CustomerAuthController::class, 'storeReport']);




    
