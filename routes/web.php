<?php

use App\Http\Controllers\WEB\LoginController;
use App\Http\Controllers\WEB\NotificationController;
use Illuminate\Http\Request;
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

// Route::group(["middleware" => "web"], function () {
    Route::get('/', function (Request $request) {
        if ($request->session()->has('username')) {
            return redirect("/send-notification");
        } else {
            return redirect("/login");
        }
    });

    Route::get('/login', [LoginController::class, 'index']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/send-notification', [NotificationController::class, 'index']);
    Route::post('/notification/send', [NotificationController::class, 'send']);

    Route::get('/delete', function () {
        return view('admin/frontend/delete/delete_user_ac');
    });

    Route::get('/deleteresp', function () {
        return view('admin/frontend/delete/delete_user_resp');
    });
// });





require __DIR__ . '/auth.php';
