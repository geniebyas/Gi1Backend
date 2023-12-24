<?php

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\IndustryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });

//test
Route::get("/test", function () {
    p("test called");
});


//user
// Route::post('user/store',function(Request $request){
//     return UserController->store($request);
// }
// );
//user group
Route::prefix('user')->group(function () {
    Route::post('/register', [UserController::class, 'store']);
    Route::put('/registration/{uid}', [UserController::class, 'update']);
    Route::get('/users', [UserController::class, 'getAllUsers']);
    // Route::get('/{uid}',[UserController::class,'show']);
    Route::delete('/{uid}', [UserController::class, 'destroy']);
    Route::put('/{uid}', [UserController::class, 'update']);
    Route::get('/checkuserexists/{uid}', [UserController::class, 'checkUserExists']);
});

//industry group
Route::prefix('industry')->group(function () {
    Route::post('/add', [IndustryController::class, 'create']);
    Route::get('/active',[IndustryController::class, 'allActiveIndustries']);
});
