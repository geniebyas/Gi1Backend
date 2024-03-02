<?php

use App\Http\Controllers\API\CoinsController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\IndustryController;
use App\Http\Controllers\API\FileUploadController;
use App\Http\Controllers\API\SettingController;
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

Route::group(['middleware' => "api"], (function () {

    //files
    Route::prefix("file")->group(function () {
        Route::post('/upload', [FileUploadController::class, 'fromApi']);
    });

    //users
    Route::prefix('user')->group(function () {
        Route::post('/register', [UserController::class, 'store']);
        Route::put('/registration/{uid}', [UserController::class, 'update']);
        Route::get('/users', [UserController::class, 'getAllUsers']);
        Route::get('/{uid}', [UserController::class, 'show']);
        Route::post('/deleteuserac', [UserController::class, 'destroy']);
        Route::put('/{uid}', [UserController::class, 'update']);
        Route::get('/checkuserexists/{uid}', [UserController::class, 'checkUserExists']);
        Route::get('/isuniqueuser/{username}',[UserController::class,'isuniqueuser']);
    });

    Route::prefix('setting')->group(function (){
        Route::get('/referal/isvalid/{code}',[SettingController::class,'isvalid']);

    });

    //industry group
    Route::prefix('industry')->group(function () {
        Route::post('/add', [IndustryController::class, 'create']);
        Route::get('/active', [IndustryController::class, 'allActiveIndustries']);
    });

    //coins
    Route::prefix('coins')->group(function () {
        Route::post('/add',[CoinsController::class, 'create']);
        Route::get('/userwallet',[CoinsController::class, 'getCoinsDetailsForUser']);

    });





}));
