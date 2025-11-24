<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('make-hash', function () {
    $hash = Hash::make('123456');
    dd($hash);
});
