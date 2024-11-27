<?php

use App\Http\Controllers\Auth\Socials\GoogleController;
use App\Http\Controllers\Auth\Socials\TwitterController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


// google auth routes
Route::get('/auth/google/redirect', [GoogleController::class,'handleGoogleRedirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class,'handleGoogleCallback']);

//twitter auth routes
Route::get('/auth/twitter/redirect', [TwitterController::class,'handleTwitterRedirect'])->name('x.redirect');
Route::get('/auth/twitter/callback', [TwitterController::class,'handleTwitterCallback']);
