<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AwsTest;

Route::get('/', function () {
    // return view('welcome');
});
Route::get('/textTract', [AwsTest::class, 'textTract']);

