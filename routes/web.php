<?php

Route::get('/', function () {

    return view('fundbridge');

})->name('home');



Route::get('/register', function () {

    return view('register');

})->name('register');



Route::get('/login', function () {

    return view('login');

})->name('login');
