<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CoinsController;
use App\Http\Controllers\API\ConnectionsController;
use App\Http\Controllers\API\FeedbackController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\IndustryController;
use App\Http\Controllers\API\FileUploadController;
use App\Http\Controllers\API\LeaderBoardController;
use App\Http\Controllers\API\NotificationController;
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
Route::post("/git-deploy",[AuthController::class,'gitDeploy']);


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
        Route::post('/registration', [UserController::class, 'update']);
        Route::get('/publicusers', [UserController::class, 'getAllPublicUsers']);
        Route::get('/{uid}', [UserController::class, 'show']);
        Route::post('/deleteuserac', [UserController::class, 'destroy']);
        Route::get('/checkuserexists/{uid}', [UserController::class, 'checkUserExists']);
        Route::get('/isuniqueuser/{username}',[UserController::class,'isuniqueuser']);
        Route::post('/update',[UserController::class,'updateUser']);
        Route::post('/tokenupdate',[UserController::class,'tokenUpdate']);
    });

    //settings
    Route::prefix('settings')->group(function (){
        Route::get('/referal/isvalid/{refer_code}',[SettingController::class,'isvalid']);
        Route::post('/add',[SettingController::class,'add_setting']);
        Route::post('/update',[SettingController::class,'updateSetting']);

    });

    //industry
    Route::prefix('industry')->group(function () {
        Route::post('/add', [IndustryController::class, 'create']);
        Route::get('/active', [IndustryController::class, 'allActiveIndustries']);
        Route::get('/industryitem/{id}',[IndustryController::class,'getIndustryItem']);
        Route::post('/adddiscussion',[IndustryController::class,'addDiscussion']);
        Route::post('/addreply',[IndustryController::class,'addReply']);
        Route::get('/discussionlike/{discussion_id}',[IndustryController::class,'discussionLike']);
        Route::get('/replylike/{reply_id}',[IndustryController::class,'replyLike']);
    });

    //coins
    Route::prefix('coins')->group(function () {
        Route::post('/add',[CoinsController::class, 'create']);
        Route::get('/userwallet',[CoinsController::class, 'getCoinsDetailsForUser']);
        Route::get('actions',[CoinsController::class, 'getCoinsActions']);

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
        Route::get('/explore',[SearchController::class,'getExplore']);
    });

    //connections
    Route::prefix('connect') ->group(function (){
        Route::get('/userconnections',[ConnectionsController::class,'getUserConnections']);
        Route::get('/sendrequest/{dest_uid}',[ConnectionsController::class,'sendFriendRequest']);
        Route::get('/deleterequest/{dest_uid}',[ConnectionsController::class,'deleteRequest']);
        Route::get('/userinfo/{uid}',[ConnectionsController::class,'getUserWithDetails']);
        Route::get('/pendingrequest',[ConnectionsController::class,'getPendingRequest']);
        Route::get('/updaterequest/{id}/{status}',[ConnectionsController::class,'updateRequest']);
        Route::get('/userrelations/{dest_uid}',[ConnectionsController::class,'getUserRelation']);
    });

    //leaderboard
    Route::prefix('/leaderboard') -> group(function (){
        Route::get('/top50',[LeaderBoardController::class,'getLeaderBoard']);

    });

    //notification
    Route::prefix('/notification') -> group(function (){
        Route::post('/send',[NotificationController::class,'sendNotification']);
        Route::get('/all',[NotificationController::class,'getNotifications']);
        Route::get('/announcement',[NotificationController::class,'getAnnouncement']);
    });

    //links
    Route::prefix('/links')->group(function (){
       Route::post('/add',[\App\Http\Controllers\API\LinksController::class,'addLink']);
       Route::post('/update',[\App\Http\Controllers\API\LinksController::class,'updateLink']);
       Route::get('/delete/{id}',[\App\Http\Controllers\API\LinksController::class,'deleteLink']);
       Route::get('/registerclick/{id}',[\App\Http\Controllers\API\LinksController::class,'registerLinkClick']);
    });

    //cdsm
    Route::prefix('/cdsm')->group(function (){
        Route::post('/add',[\App\Http\Controllers\API\CDSMController::class,'addPost']);
        Route::get('/all',[\App\Http\Controllers\API\CDSMController::class,'getPosts']);
        Route::get('/analytics/{id}',[\App\Http\Controllers\API\CDSMController::class,'loadAnalytics']);
        Route::get('/toggle-like/{id}',[\App\Http\Controllers\API\CDSMController::class,'toggleLike']);
        Route::get('/my-posts',[\App\Http\Controllers\API\CDSMController::class,'myPosts']);
        Route::get('/delete/{id}',[\App\Http\Controllers\API\CDSMController::class,'deletePost']);
    });



}));
