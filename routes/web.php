<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth::routes();

Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('show.login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegister'])->name('show.register');
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register');
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');


Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/timesheet', [App\Http\Controllers\TimesheetController::class, 'index'])->name('timsheet.index');
Route::get('/timesheet/{id}', [App\Http\Controllers\TimesheetController::class, 'show'])->name('timsheet.show');
Route::get('/timesheet-detail', [App\Http\Controllers\TimesheetController::class, 'showByDate'])->name('timsheet.show.by.date');
Route::post('/timesheet', [App\Http\Controllers\TimesheetController::class, 'store'])->name('timsheet.create');
Route::put('/timesheet/{id}', [App\Http\Controllers\TimesheetController::class, 'update'])->name('timesheet.update');
Route::put('/check-in', [App\Http\Controllers\TimesheetController::class, 'checkIn'])->name('user.check.in');
Route::put('/check-out', [App\Http\Controllers\TimesheetController::class, 'checkOut'])->name('user.check.out');
