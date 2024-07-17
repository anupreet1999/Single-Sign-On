<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SSOAuthentication;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('sso-login', [AuthController::class, 'ssoLogin']);
Route::get('user', [AuthController::class, 'getUser']);

// Get Token from Provider And Verify Token 
Route::post('sso/authentication', [SSOAuthentication::class, 'verifyProviderToken']);
