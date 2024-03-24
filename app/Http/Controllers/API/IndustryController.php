<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use App\Models\IndustryDiscussion;
use App\Models\IndustryDiscussionLike;
use App\Models\IndustryReply;
use App\Models\IndustryReplyLike;
use App\Models\IndustryView;
use App\Models\PersonalNotification;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class IndustryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    }

    public function allActiveIndustries()
    {
        $list = Industry::get();
        if (count($list) > 0) {
            //users exists
            $response = [
                'message' => count($list) . ' industries found',
                'status' => 1,
                'data' => $list
            ];
        } else {
            $response = [
                'message' => count($list) . ' industries found',
                'status' => 1,
                'data' => null
            ];
        }
        return response()->json($response, 200);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $uid = $request->header('uid');
        $industry = new Industry();
        $client = new Client();

        $resp = $client->request('POST', "https://api.gi1superapp.com/api/file/upload", [
            'headers' => [
                'uid' => $uid
            ],
            'multipart' => [
                'dir' => $request->dir,
                'name' => $request->name,
                'contents' => Psr7\Utils::tryFopen($request->file($request->name)->path(), 'r'),
                'filename' => $request->name . ".png"
            ]
        ]);

        return response()->json([
            'message' => $resp
        ]);




        // if(!is_null($industry))
        // DB::beginTransaction();
        //     try {
        //         // Industry::create($industry);
        //         DB::commit();
        //     } catch (\Throwable $th) {
        //         //throw $th;
        //         DB::rollBack();
        //         $industry = null;
        //         $e = $th;
        //     }
        //     if ($industry != null) {
        //         return response()->json(
        //             [
        //                 "message" => "Industry Added successfully",
        //                 "status" => 1,
        //                 "data" => $industry
        //             ],
        //             200
        //         );
        //     } else {
        //         return response()->json(
        //             [
        //                 'message' => "Error Occured" . $e->getMessage() ,
        //                 'status' => 0,
        //                 'data' =>$request->all()
        //             ],
        //             500
        //         );
        //     }
        p($request->all());
    }

    public function getIndustryItem(Request $request, $id)
    {

        $uid = $request->header('uid');

        if (!IndustryView::where('industry_id', $id)->where('uid', $uid)->exists()) {
            IndustryView::create([
                'industry_id' => $id,
                'uid' => $uid
            ]);
            $industry = Industry::find($id);
            addCoins($uid, 6, "You got a coins for visiting $industry->name.");
        } else {
            $view = IndustryView::where('industry_id', $id)->where('uid', $uid)->get()->first();
            $view->updated_at = time();
            $view->update();
        }

        $industry = Industry::with(['discussions' => function ($query) {
            $query->with('user')
                ->with(['likes' => function ($query) {
                    $query->with('user');
                }])
                ->with(['replies' => function ($query) {
                    $query->with('user')
                        ->with(['likes' => function ($query) {
                            $query->with('user');
                        }]);
                }]);
        }])
            ->find($id);





        if ($industry != null) {
            if ($industry->discussions != null) {
                foreach ($industry->discussions as $d) {
                    if ($d->likes != null) {
                        foreach ($d->likes as $l) {
                            if ($l->uid == $uid) {
                                $d->is_liked = true;
                            } else {
                                $d->is_liked = false;
                            }
                        }
                    }


                    if ($d->replies != null) {
                        foreach ($d->replies as $r) {
                            if ($r->likes != null) {
                                foreach ($r->likes as $rl) {
                                    if ($r->uid == $uid) {
                                        $r->is_liked = true;
                                    } else {
                                        $r->is_liked = false;
                                    }
                                }
                            }
                        }
                    }
                }
            }


            return response()->json(
                [
                    'message' => 'Industry loaded Successfully',
                    'status' => 1,
                    'data' => $industry
                ]
            );
        } else {
            return response()->json(
                [
                    'message' => 'Some Error Occurred',
                    'status' => 0,
                    'data' => "Error Detected"
                ],
                500
            );
        }
    }

    public function addDiscussion(Request $request)
    {
        $uid = $request->header('uid');
        $industry_id = $request->industry_id;
        $msg = $request->msg;

        $res = IndustryDiscussion::create([
            'uid' => $uid,
            'industry_id' => $industry_id,
            'msg' => $msg
        ]);

        return response()->json([
            'message' => 'Discussion created successfully',
            'status' => 1,
            'data' => $res
        ]);
    }

    public function addReply(Request $request)
    {
        $uid = $request->header('uid');
        $discussion_id = $request->discussion_id;
        $msg = $request->msg;

        $res = IndustryReply::create([
            'uid' => $uid,
            'discussion_id' => $discussion_id,
            'msg' => $msg
        ]);


        $dis = IndustryDiscussion::find($discussion_id);
        if ($dis->uid != $uid) {
            $user = User::where("uid", $uid)->get()->first();
            $industry = Industry::find($dis->industry_id);

            sendPersonalNotification(new PersonalNotification([
                'sender_uid' => $uid,
                'receiver_uid' => $dis->uid,
                "title" => "Reply In $industry->name Discussion",
                "body" => "$user->username replied you in $industry->name"
            ]));
        }

        return response()->json([
            'message' => 'Reply created successfully',
            'status' => 1,
            'data' => $res
        ]);
    }

    public function discussionLike(Request $request, $discussion_id)
    {
        $uid = $request->header("uid");

        if (IndustryDiscussionLike::where('uid', $uid)->where('discussion_id', $discussion_id)->exists()) {
            $like = IndustryDiscussionLike::where('uid', $uid)->where('discussion_id', $discussion_id)->get()->first();
            $resp = $like->delete();
        } else {
            $resp = IndustryDiscussionLike::create([
                'uid' => $uid,
                'discussion_id' => $discussion_id
            ]);

            $dis = IndustryDiscussion::find($discussion_id);

            if ($uid != $dis->uid) {
                $user = User::where("uid", $uid)->get()->first();
                $industry = Industry::find($dis->industry_id);

                sendPersonalNotification(new PersonalNotification([
                    'sender_uid' => $uid,
                    'receiver_uid' => $dis->uid,
                    "title" => "Like In $industry->name Discussion",
                    "body" => "$user->username liked your discussion in $industry->name"
                ]));
            }
        }


        return response()->json([
            'message' => "Successfull",
            'status' => 1,
            'data' => true
        ]);
    }
    public function replyLike(Request $request, $reply_id)
    {
        $uid = $request->header("uid");

        if (IndustryReplyLike::where('uid', $uid)->where('reply_id', $reply_id)->exists()) {
            $like = IndustryReplyLike::where('uid', $uid)->where('reply_id', $reply_id)->get()->first();
            $resp = $like->delete();
        } else {
            $resp = IndustryReplyLike::create([
                'uid' => $uid,
                'reply_id' => $reply_id
            ]);

            $reply = IndustryReply::find($reply_id);
            $dis = IndustryDiscussion::find($reply->discussion_id);

            if ($uid != $reply->uid) {
                $user = User::where("uid", $uid)->get()->first();
                $industry = Industry::find($dis->industry_id);

                sendPersonalNotification(new PersonalNotification([
                    'sender_uid' => $uid,
                    'receiver_uid' => $reply->uid,
                    "title" => "Like in $industry->name Discussion",
                    "body" => "$user->username liked your reply in $industry->name"
                ]));
            }
        }

        return response()->json([
            'message' => "Successfull",
            'status' => 1,
            'data' => true
        ]);
    }
}
