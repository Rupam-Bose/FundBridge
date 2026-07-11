<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;


/*
 Public Pages
*/

Route::get('/', function () {

    return view('fundbridge');

})->name('home');



Route::get('/register', function () {

    return view('register');

})->name('register');



Route::get('/login', function () {

    return view('login');

})->name('login');


/*
Authentication Actions
*/

Route::post(
    '/register',
    [RegisterController::class, 'register']
)->name('register.submit');


Route::post(
    '/login',
    [LoginController::class, 'login']
)->name('login.submit');


Route::post(
    '/logout',
    [LoginController::class, 'logout']
)->name('logout');






/*
|--------------------------------------------------------------------------
| Founder Dashboard
|--------------------------------------------------------------------------
*/


// Route::middleware('auth')->group(function () {


//     Route::get('/founder/dashboard', function () {

//         return "Founder Dashboard";

//     })->name('founder.dashboard');



//     Route::get('/investor/dashboard', function () {

//         return "Investor Dashboard";

//     })->name('investor.dashboard');


// });


// Route::get('/founder/dashboard', function () {

//     return view('founder.dashboard');

// })->name('founder.dashboard');

Route::get('/founder/dashboard', function () {

    return view('founder.dashboard');

})
    ->middleware('auth')
    ->name('founder.dashboard');