<?php

use App\Http\Controllers\api\ObaseAuth;
use Illuminate\Http\Request;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route Structure
Route::prefix('v2/Obase/auth')->group(function () {

    // Create Account
    Route::post('create-account',[ObaseAuth::class,'createAccount'])->name('create-account');
    
    // Login Account
    Route::post('login',[ObaseAuth::class,'login'])->name('login');

// Auth senctum middlewere
    Route::middleware(['auth:sanctum'])->group(function () {

    
    });

    
});