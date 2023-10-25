<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TopUpController;
use App\Http\Controllers\Api\WebHookController;
use App\Http\Controllers\Api\DataPlanController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\PulsaPlanController;
use App\Http\Controllers\Api\VendorCardController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\TransferHistoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WalletController;

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
// // test api jwt
// Route::middleware('jwt.verify')->get('/test', function (Request $request) {
//     return 'success';
// });

Route::POST('register', [AuthController::class, 'register']);

Route::POST('login', [AuthController::class, 'login']);

Route::POST('is_email_exists', [UserController::class, 'isEmailExists']);

Route::group(['middleware' => 'jwt.verify'], function ($router) {
    Route::post('top_ups', [TopUpController::class, 'store']);

    Route::post('webhooks', [WebHookController::class, 'update']);

    Route::post('transfers', [TransferController::class, 'store']);

    Route::post('data_plans', [DataPlanController::class, 'store']);

    Route::post('pulsa_plans', [PulsaPlanController::class, 'store']);

    Route::get('vendor_cards', [VendorCardController::class, 'index']);

    Route::get('payment_methods', [PaymentMethodController::class, 'index']);

    Route::get('transfer_histories', [TransferHistoryController::class, 'index']);

    Route::get('transactions', [TransactionController::class, 'index']);

    Route::get('users', [UserController::class, 'show']);
    Route::get('users/{username}', [UserController::class, 'getUserByUsername']);
    Route::put('users', [UserController::class, 'update']);
    
    Route::get('wallets', [WalletController::class, 'show']);
    Route::put('wallets', [WalletController::class, 'update']);
    
    Route::post('logout', [AuthController::class, 'logout']);
});