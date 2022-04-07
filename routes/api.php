<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route; 

Route::post('register', [UserController::class,'insert']);
Route::post('login', [UserController::class,'login']);


// ALL CAN ACCESS
Route::group(['middleware' => ['jwt.verify:admin,kasir,owner']], function () {

    Route::get('login/check', [UserController::class,'loginCheck']);
    Route::post('logout', [UserController::class,'logout']);

    Route::get('outlet', [OutletController::class,'getAll']);
    Route::get('outlet/{id_outlet}', [OutletController::class,'getByID']);

    // Report Transaksi
    Route::post('transaksi/report', [TransaksiController::class, 'report']);

});

// ADMIN AND KASIR ONLY
Route::group(['middleware' => ['jwt.verify:admin,kasir']], function() { 

    // Member
    Route::post('member', [MemberController::class,'insert']);
    Route::put('member/{id_member}', [MemberController::class,'update']);
    Route::delete('member/{id_member}', [MemberController::class,'delete']);
    Route::get('member', [MemberController::class,'getAll']);
    Route::get('member/{id_member}', [MemberController::class,'getByID']);

    Route::post('transaksi', [TransaksiController::class,'insert']);
    Route::put('transaksi/status', [TransaksiController::class,'update_status']);
    Route::put('transaksi/bayar', [TransaksiController::class,'update_bayar']);

    Route::get('paket', [PaketController::class,'getAll']);
    Route::get('paket/{id_outlet}', [PaketController::class,'getByID']);

});

// ADMIN ONLY
Route::group(['middleware' => ['jwt.verify:admin']], function () {

    // User
    Route::post('user', [UserController::class,'insert']);
    Route::put('user/{id_user}', [UserController::class,'update']);
    Route::delete('user/{id_user}', [UserController::class,'delete']);
    Route::get('user', [UserController::class,'getAll']);
    Route::get('user/{id_user}', [UserController::class,'getByID']);

    // Outlet
    Route::post('outlet', [OutletController::class,'insert']);
    Route::put('outlet/{id_outlet}', [OutletController::class,'update']);
    Route::delete('outlet/{id_outlet}', [OutletController::class,'delete']);

    // Paket
    Route::post('paket', [PaketController::class,'insert']);
    Route::put('paket/{id_paket}', [PaketController::class,'update']);
    Route::delete('paket/{id_paket}', [PaketController::class,'delete']);

});