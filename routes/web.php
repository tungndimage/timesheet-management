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

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/timesheet', [App\Http\Controllers\TimesheetController::class, 'index'])->name('timsheet.index');
Route::get('/timesheet/{id}', [App\Http\Controllers\TimesheetController::class, 'show'])->name('timsheet.show');
Route::get('/timesheet-detail',[App\Http\Controllers\TimesheetController::class, 'showByDate'] )->name('timsheet.show.by.date');
