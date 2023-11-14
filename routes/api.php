<?php

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
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [\App\Http\Controllers\AuthController::class, 'getUser']);
    Route::get('/logout', [\App\Http\Controllers\AuthController::class, 'destroy']);
    Route::post('/query', [\App\Http\Controllers\QueryController::class, 'run']);
    Route::get('/query/restore', [\App\Http\Controllers\QueryController::class, 'restoreDatabase']);

    Route::resource('posts', \App\Http\Controllers\PostsController::class);
    Route::resource('categories', \App\Http\Controllers\CategoriesController::class);
    Route::resource('roles', \App\Http\Controllers\RolesController::class);
    Route::resource('users', \App\Http\Controllers\UsersController::class);

    Route::post('/dictionaries/{dictionaryName}', [\App\Http\Controllers\DictionaryController::class, 'getDictionary']);
//Route::post('/user', [\App\Http\Controllers\UserController::class, 'run']);
});


