<?php

use App\Http\Controllers\API\CoinsController;
use App\Http\Controllers\API\ConnectionsController;
use App\Http\Controllers\API\FeedbackController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\IndustryController;
use App\Http\Controllers\API\FileUploadController;
use App\Http\Controllers\API\SearchController;
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
        Route::post('/registration/{uid}', [UserController::class, 'update']);
        Route::get('/publicusers', [UserController::class, 'getAllPublicUsers']);
        Route::get('/{uid}', [UserController::class, 'show']);
        Route::post('/deleteuserac', [UserController::class, 'destroy']);
        Route::get('/checkuserexists/{uid}', [UserController::class, 'checkUserExists']);
        Route::get('/isuniqueuser/{username}',[UserController::class,'isuniqueuser']);
    });

    Route::prefix('settings')->group(function (){
        Route::get('/referal/isvalid/{refer_code}',[SettingController::class,'isvalid']);
        Route::post('/add',[SettingController::class,'add_setting']);

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

    //feedback
    Route::prefix('feedback')->group(function (){
        Route::get('/categories',[FeedbackController::class,'getAllFeedback']);
        Route::post('/submit',[FeedbackController::class,'submitFeedbackAnswer']);
        Route::get('/category/{id}',[FeedbackController::class,'getCategory']);
    });


    //search
    Route::prefix('search') ->group(function (){
        Route::get('/global/{query}/{filter}',[SearchController::class,'globalSearch']);
    });

    //connections
    Route::prefix('connect') ->group(function (){
        Route::get('/userconnections',[ConnectionsController::class,'getUserConnections']);
        Route::post('/sentrequest/{dest_uid}',[ConnectionsController::class,'sendFriendRequest']);
        Route::get('/userinfo/{uid}',[ConnectionsController::class,'getUserWithDetails']);
    });


}));
