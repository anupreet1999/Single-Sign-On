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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// SSO Via provide creds
Route::post('verify-token', [AuthController::class, 'verifyToken']);
Route::middleware('auth:api')->get('/sso-validate', [AuthController::class, 'ssoValidate']);


//  SSO Authentication via sendding token 
Route::post('sso/send-token', [SSOAuthentication::class, 'sendToken']);
Route::post('sso/verify-token', [SSOAuthentication::class, 'verifyToken']);