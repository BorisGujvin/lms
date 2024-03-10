<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AccountController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\CreditController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::name('api.')->group(function () {
   Route::resource('accounts', AccountController::class);
   Route::resource('transactions', TransactionController::class);
   Route::group(['prefix' => 'credits', 'as' => 'credits.'], function () {
       Route::post('/', [CreditController::class, 'create']);
       Route::get('/{id}', [CreditController::class, 'show']);
       Route::post('/{id}/interest', [CreditController::class, 'calculateInterest']);
       Route::post('/{id}/payment', [CreditController::class, 'paymentReceived']);

   });
});

