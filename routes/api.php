<?php

use Illuminate\Http\Request;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CenterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientTypeController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TemplateTypesController;
use App\Http\Controllers\UserTypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

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

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/centers', [CenterController::class, 'GetAll']);
Route::get('/user-type', [UserTypeController::class, 'GetAll']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::get('/patient-types', [PatientTypeController::class, 'GetAll']);
    Route::get('/template-types', [TemplateTypesController::class, 'GetAll']);

    //brand
    Route::get('/brand', [BrandController::class, 'GetActive']);
    Route::get('/brand/all', [BrandController::class, 'GetAll']);
    Route::get('/brand/{id}', [BrandController::class, 'GetById']);
    Route::post('/brand', [BrandController::class, 'Store']);
    Route::put('/brand/{id}', [BrandController::class, 'Update']);
    Route::delete('/brand/{id}', [BrandController::class, 'SoftDelete']);

    //category
    Route::get('/category', [CategoryController::class, 'GetActive']);
    Route::get('/category/all', [CategoryController::class, 'GetAll']);
    Route::post('/category', [CategoryController::class, 'Store']);
    Route::put('/category/{id}', [CategoryController::class, 'Update']);
    Route::delete('/category/{id}', [CategoryController::class, 'SoftDelete']);

    //patient
    Route::get('/patient', [PatientController::class, 'GetActive']);
    Route::get('/patient/all', [PatientController::class, 'GetAll']);
    Route::post('/patient', [PatientController::class, 'Store']);
    Route::put('/patient/{id}', [PatientController::class, 'Update']);
    Route::delete('/patient/{id}', [PatientController::class, 'SoftDelete']);

    //product
    Route::get('/product', [ProductsController::class, 'GetActive']);
    Route::get('/product/all', [ProductsController::class, 'GetAll']);
    Route::post('/product', [ProductsController::class, 'Store']);
    Route::put('/product/{id}', [ProductsController::class, 'Update']);
    Route::delete('/product/{id}', [ProductsController::class, 'SoftDelete']);

    //stock
    Route::get('/stock/{centerid}', [StockController::class, 'GetAll']);
    // Route::get('/stock/all', [StockController::class, 'GetAll']);
    Route::post('/stock', [StockController::class, 'Store']);
    // Route::put('/stock/{id}', [StockController::class, 'Update']);
    // Route::delete('/stock/{id}', [StockController::class, 'SoftDelete']);
    
    //supplier
    Route::get('/supplier', [SupplierController::class, 'GetActive']);
    Route::get('/supplier/all', [SupplierController::class, 'GetAll']);
    Route::post('/supplier', [SupplierController::class, 'Store']);
    Route::put('/supplier/{id}', [SupplierController::class, 'Update']);
    Route::delete('/supplier/{id}', [SupplierController::class, 'SoftDelete']);
    
    //template
    Route::get('/template/{templateTypeId}', [TemplateController::class, 'GetActiveByTypes']);
    Route::get('/template/all/{centerid}', [TemplateController::class, 'GetAll']);
    Route::post('/template', [TemplateController::class, 'Store']);
    Route::put('/template/{id}', [TemplateController::class, 'Update']);
    Route::delete('/template/{id}', [TemplateController::class, 'SoftDelete']);
    
    //doctor
    Route::get('/doctor', [AuthController::class, 'GetActiveByTypes']);
    Route::get('/doctor/all', [AuthController::class, 'GetAll']);
    
    //Invoice
    Route::post('/invoice', [InvoiceController::class, 'Index']);
});

Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('route:clear');
    return 'Cache Cleared';
});

Route::get('/migrate', function () {
    $exitCode = Artisan::call('migrate');
    return 'Database migrated successfully';
});

Route::get('/migrate/rollback-all', function () {
    $exitCode = Artisan::call('migrate:rollback');
    return 'Database rollback successfully';
});
