<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TumblerController;

Route::get('/', function () {
    return view('pages.home');
})->name('home');   

Route::get('/tumbler', function () {
    return view('pages.plp');
})->name('plp');  

Route::get('/tumbler/{i}', function () {
    return view('pages.pdp');
})->name('pdp');  