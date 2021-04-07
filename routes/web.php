<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadedFileController;

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
Route::redirect('/', '/dashboard');

Route::get('/dashboard', [UploadedFileController::class, 'index'])->middleware(['auth'])->name('dashboard');
Route::post("uploadFiles", [UploadedFileController::class, 'store'])->middleware(['auth'])->name("uploadFiles");

require __DIR__.'/auth.php';
