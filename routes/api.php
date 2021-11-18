<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;


Route::post('/user/register', [UserController::Class,'register'])->name('register');
Route::post('/user/login', [UserController::Class,'login'])->name('login');
Route::post('/user/verify', [UserController::Class,'verify']);



Route::group(['middleware' => ['auth:api']], function (){
    Route::post('/invitation', [UserController::Class,'sendInvitation']);
});

Route::fallback(function () {
    return response()->json(['Status' => false, 'ErrorCode' => "RT404", 'Error' => 'Route does not exist']);
});
