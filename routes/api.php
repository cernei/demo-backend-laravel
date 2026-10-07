<?php

use App\Http\Controllers\AccessLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\JobTestController;
use App\Http\Controllers\QueryController;
use Illuminate\Http\Request;
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
Route::post('/login', [AuthController::class, 'store'])->name('login');
Route::post('/admin/login', [AuthenticatedSessionController::class, 'storeAdmin'])
                ->middleware('guest:admin')
                ->name('admin.login');

Route::post('/webhooks/invoices', [InvoicesController::class,'store']);

Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroyAdmin']);
    Route::post('/access_log', [AccessLogController::class, 'trackView']);
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::get('/logout', [AuthController::class, 'destroy']);
    Route::post('/query', [QueryController::class, 'run']);
//    Route::get('/query/restore', [QueryController::class, 'restoreDatabase']);

    Route::resource('products', \App\Http\Controllers\ProductsController::class);
    Route::resource('categories', \App\Http\Controllers\CategoriesController::class);
    Route::resource('roles', \App\Http\Controllers\RolesController::class);
    Route::resource('users', \App\Http\Controllers\UsersController::class);

    Route::post('/dictionaries/{dictionaryName}', [DictionaryController::class, 'getDictionary']);

    Route::get('/data', [HomeController::class, 'data']);

    Route::get('/clear-all', [HomeController::class, 'clearAll']);

    Route::get('/shipment-job-test', [JobTestController::class, 'shipment']);
});


