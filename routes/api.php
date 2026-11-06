<?php

use App\Http\Controllers\AccessLogController;
use App\Http\Controllers\AdminsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\JobTestController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\QueryController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\StockItemsController;
use App\Http\Controllers\StockLocationsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/admin/login', [AuthenticatedSessionController::class, 'storeAdmin'])
                ->middleware('guest:admin')
                ->name('admin.login');

Route::post('/webhooks/invoices', [InvoicesController::class,'store']);

Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('/logout', [AuthenticatedSessionController::class, 'destroyAdmin']);
    Route::post('/access_log', [AccessLogController::class, 'trackView']);
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/query', [QueryController::class, 'run']);
//    Route::get('/query/restore', [QueryController::class, 'restoreDatabase']);

    Route::resource('stock-locations', StockLocationsController::class)->except(['show', 'index', 'create']);
    Route::resource('stock-items', StockItemsController::class)->except(['show', 'index', 'create']);
    Route::resource('orders', OrdersController::class)->except(['show', 'index', 'create']);
    Route::resource('products', ProductsController::class)->except(['show', 'index', 'create']);
    Route::resource('categories', CategoriesController::class)->except(['show', 'index', 'create']);
    Route::resource('roles', RolesController::class)->except(['show', 'index', 'create']);
    Route::resource('admins', AdminsController::class)->except(['show', 'index', 'create']);

    Route::post('/dictionaries/{dictionaryName}', [DictionaryController::class, 'getDictionary']);

    Route::get('/data', [HomeController::class, 'data']);

    Route::get('/clear-all', [HomeController::class, 'clearAll']);

    Route::get('/shipment-job-test', [JobTestController::class, 'shipment']);
});


