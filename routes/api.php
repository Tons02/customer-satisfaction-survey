<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;

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

//Role Controller
Route::resource("role", RoleController::class);
Route::put('role-archived/{id}',[RoleController::class,'archived']);

//Users Controller
Route::resource("users", UserController::class);
Route::put('user-archived/{id}',[UserController::class,'archived']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
