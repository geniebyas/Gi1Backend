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

Route::get('/', function () {
    return view('admin/frontend/login');
});

Route::get('/delete', function () {
    return view('admin/frontend/delete/delete_user_ac');
});

Route::get('/deleteresp', function () {
    return view('admin/frontend/delete/delete_user_resp');
});


require __DIR__.'/auth.php';
