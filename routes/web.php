<?php

use App\Http\Controllers\backend\AboutController;
use App\Http\Controllers\backend\HeroBannerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('backend.index');
});




Route::resource('banners', HeroBannerController::class)
        ->names('hero-banners')
        ->parameter('banners', 'post');
Route::resource('about', AboutController::class)
        ->names('about')
        ->parameter('about', 'post');
