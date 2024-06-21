<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;

Route::get('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);
Route::post('/authenticate', [AuthController::class, 'authenticate']);
Route::get('/user', [AuthController::class, 'user'])->middleware('auth');
Route::get('/form-user/{id}', [AuthController::class, 'formUser'])->middleware('auth');
Route::get('/delete-user/{id}', [AuthController::class, 'deleteUser'])->middleware('auth');
Route::post('/save-user', [AuthController::class, 'saveUser'])->middleware('auth');

Route::get('/', [MainController::class, 'board'])->middleware('auth');
Route::get('/atp', [MainController::class, 'atp'])->middleware('auth');
Route::get('/form-atp/{id}', [MainController::class, 'formAtp'])->middleware('auth');
Route::get('/delete-atp/{id}', [MainController::class, 'deleteAtp'])->middleware('auth');
Route::get('/download-atp/{id}', [MainController::class, 'downloadAtp'])->middleware('auth');
Route::get('/download-history/{id}', [MainController::class, 'downloadHistory'])->middleware('auth');
Route::get('/detail-atp/{id}', [MainController::class, 'detailAtp'])->middleware('auth');
Route::post('/save-atp', [MainController::class, 'saveAtp'])->middleware('auth');
Route::post('/status', [MainController::class, 'status'])->middleware('auth');
