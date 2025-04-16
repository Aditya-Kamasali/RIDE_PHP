<?php

use App\Http\Controllers\DriverController;
use App\Http\Controllers\logincontroller;
use App\Http\Controllers\TripController;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login',[logincontroller::class,'submit'] );
Route::post('/login/verify',[logincontroller::class,'verify'] );

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/driver',[DriverController::class,'show']);
    Route::post('/driver',[DriverController::class,'update']);

    Route::post('/trip',[TripController::class,'store']);
    Route::get('/trip/{trip}',[TripController::class,'show']);
    Route::post('/trip/{trip}/accept',[TripController::class,'accept']);
    Route::post('/trip/{trip}/start',[TripController::class,'start']);
    Route::post('/trip/{trip}/end',[TripController::class,'end']);
    Route::post('/trip/{trip}/location',[TripController::class,'location']);

    Route::get('/user',function(Request $request)
    {
        return $request->user();
    });
});

