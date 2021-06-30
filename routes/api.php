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

//Route::get('/', function () {
//    return view('welcome');
//})->middleware(['auth.shopify'])->name('home');

Route::post('post',[\App\Http\Controllers\OrderController::class,'add']);
Route::get('list',[\App\Http\Controllers\OrderController::class,'listData']);
////This will redirect user to login page.
//Route::get('/login', function () {
//    if (Auth::user()) {
//        return redirect()->route('home');
//    }
//    return view('login');
//})->name('login');
