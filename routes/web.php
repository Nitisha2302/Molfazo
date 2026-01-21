<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GlobalSearchController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\StoreController;;

Route::fallback(function () {
    return response()->view('404', [], 404);
});
Route::get('/', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::get('/login', function () {
    return redirect('/');
})->name('login.redirect');
Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [AdminAuthController::class, 'forgotPassword'])->name('forgot-password');
Route::post('/forgot-password-link', [AdminAuthController::class, 'forgotPasswordLink'])->name('forgot-password-link');
Route::post('/create-new-password', [AdminAuthController::class, 'createNewPassword'])->name('create-new-password');
Route::get('reset-password/{token}', [AdminAuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/update-password', [AdminAuthController::class, 'updatePassword'])->name('update-password');
Route::get('/global-search', [GlobalSearchController::class, 'search'])->name('global-search');

Route::get('/delete-account/{id?}', [AdminAuthController::class, 'showDeleteAccountPage'])->name('delete-account.page');

Route::post('/delete-account-confirm', [AdminAuthController::class, 'confirmDeleteAccount'])->name('delete-account.confirm');




// Routes with the same prefix for both Admin and Investor
Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    // Admin Dashboard
    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'role:1']], function () {
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
        // Categories
    Route::get('categories', [CategoryController::class,'categoryListing'])->name('categories');
    Route::get('categories/create', [CategoryController::class,'createCategory'])->name('categories.create');
    Route::post('categories', [CategoryController::class,'storeCategory'])->name('categories.store');
    Route::get('categories/{id}/edit', [CategoryController::class,'editCategory'])->name('categories.edit');
    Route::put('categories/{id}', [CategoryController::class,'updateCategory'])->name('categories.update');

    Route::delete('categories/{id}', [CategoryController::class,'destroyCategory'])->name('categories.delete');

    // Sub Categories
    Route::get('sub-categories', [CategoryController::class,'subCategoryListing'])->name('subcategories');
    Route::get('sub-categories/create', [CategoryController::class,'createSubCategory'])->name('subcategories.create');
    Route::post('sub-categories', [CategoryController::class,'storeSubCategory'])->name('subcategories.store');
    Route::get('sub-categories/{id}/edit', [CategoryController::class,'editSubCategory'])->name('subcategories.edit');
    Route::put('sub-categories/{id}', [CategoryController::class,'updateSubCategory'])->name('subcategories.update');
    Route::delete('sub-categories/{id}', [CategoryController::class,'destroySubCategory'])->name('subcategories.delete');



    Route::get('/vendors', [VendorController::class, 'index'])->name('vendors');

    Route::post('/vendors/{vendor}/approve', [VendorController::class, 'approve'])->name('vendors.approve');
    Route::post('/vendors/{vendor}/reject', [VendorController::class, 'reject'])->name('vendors.reject');
    Route::post('/vendors/{vendor}/block', [VendorController::class, 'block'])->name('vendors.block');
    Route::post('/vendors/{vendor}/unblock', [VendorController::class, 'unblock'])->name('vendors.unblock');


    Route::get('/stores', [StoreController::class, 'index'])->name('stores');

    Route::post('/stores/{store}/approve', [StoreController::class, 'approve'])->name('stores.approve');
    Route::post('/stores/{store}/reject', [StoreController::class, 'reject'])->name('stores.reject');


    });  

    

});

