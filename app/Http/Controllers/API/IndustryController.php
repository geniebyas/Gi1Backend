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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class IndustryController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function allActiveIndustries()
    {
        $list = Industry::where('status',true)
        ->where('type','in',['top','main'])
        ->get();
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
        Log::info('FILES', $_FILES);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string',
            'thumbnail' => 'required|file|mimes:jpeg,png,jpg,gif,svg',
            'pinnedthumb' => 'required|file|mimes:jpeg,png,jpg,gif,svg',
            'is_discussion_allowed' => 'required|boolean',
            'file' => 'nullable|file|mimes:jpeg,pdf',
            'path' => 'nullable|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 0,
                'data' =>null
            ]);
        }

        $industry = new Industry();
        $industry->name = $request->name;
        $industry->description = $request->description;
        $industry->type = $request->type;
        $industry->is_discussion_allowed = $request->is_discussion_allowed;
        $industry->status = true;
        $industry->path = $request->path ?? null;
        $industry->ispinned = false;
        Log::info($request->all());
        $industry->thumbnail = $request->file('thumbnail')->store('industry/thumbnails','public');
        $industry->pinnedthumb = $request->file('pinnedthumb')->store('industry/thumbnails','public');
        if($request->hasFile('file')){
            $industry->file = $request->file('file')->store('industry/files','public');
        }

        $industry->save();
        return response()->json([
            'message' => 'Industry Created Successfully',
            'status' => 1,
            'data' => $industry
        ]);
    }

    public function show(Request $request, $id)
    {
        $industry = Industry::with(['views.user','discussions.user','discussions.likes.user','discussions.replies.user','discussions.replies.likes.user'])->find($id);
        if ($industry != null) {
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

    public function edit(Request $request, $id)
    {
        $industry = Industry::find($id);
        if ($industry != null) {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'type' => 'required|string',
                'status' => 'required|boolean',
                'ispinned' => 'nullable|boolean',
                'thumbnail' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg',
                'pinnedthumb' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg',
                'is_discussion_allowed' => 'required|boolean',
                'file' => 'nullable|file|mimes:jpeg,pdf',
                'path' => 'nullable|string'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'status' => 0,
                    'data' =>null
                ]);
            }
            $industry->name = $request->name;
            $industry->description = $request->description;
            $industry->type = $request->type;
            $industry->is_discussion_allowed = $request->is_discussion_allowed;
            $industry->status = $request->status;
            $industry->path = $request->path ?? $industry->path;
            $industry->ispinned = $request->ispinned ?? $industry->ispinned;

            if ($request->hasFile('thumbnail')) {
                if($industry->thumbnail != null){
                    //delete old file
                    unlink(storage_path('app/public/'.$industry->thumbnail));
                }
                $industry->thumbnail = $request->file('thumbnail')->store('industry/thumbnails','public');
            }
            if ($request->hasFile('pinnedthumb')) {
                if($industry->pinnedthumb != null){
                    //delete old file
                    unlink(storage_path('app/public/'.$industry->pinnedthumb));
                }
                $industry->pinnedthumb = $request->file('pinnedthumb')->store('industry/thumbnails','public');
            }
            if($request->hasFile('file')){
                if($industry->file != null){
                    //delete old file
                    unlink(storage_path('app/public/'.$industry->file));
                }
                $industry->file = $request->file('file')->store('industry/files','public
                ');
            }
            $industry->save();
            return response()->json([
                'message' => 'Industry Updated Successfully',
                'status' => 1,
                'data' => $industry
            ]);
        }else {
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

    public function getAllIndustries()
    {
        $list = Industry::all();
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
                'data' => []
            ];
        }
        return response()->json($response);
    }

    public function analytics(Request $request)
    {
        $total_industries = Industry::count();
        $total_views = IndustryView::count();
        $total_discussions = IndustryDiscussion::count();
        $total_replies = IndustryReply::count();
        $top_5_viewed_industry = Industry::withCount('views')->orderBy('views_count', 'desc')->limit(5)->get();
        $top_5_liked_discussion = IndustryDiscussion::withCount('likes')->orderBy('likes_count', 'desc')->limit(5)->get();

        return response()->json([
            'message' => 'Analytics Loaded Successfully',
            'status' => 1,
            'data' => [
                'total_industries' => $total_industries,
                'total_views' => $total_views,
                'total_discussions' => $total_discussions,
                'total_replies' => $total_replies,
                'top_5_viewed_industry' => $top_5_viewed_industry,
                'top_5_liked_discussion' => $top_5_liked_discussion
            ]
        ]);
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

        $industry = Industry::
        with('views.user')
        ->with(['discussions' => function ($query) {
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
                                break;
                            } else {
                                $d->is_liked = false;
                            }
                        }
                    }


                    if ($d->replies != null) {
                        foreach ($d->replies as $r) {
                            if ($r->likes != null) {
                                foreach ($r->likes as $rl) {
                                    if ($rl->uid == $uid) {
                                        $r->is_liked = true;
                                        break;
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
                    "title" => "Like in $industry->name Discussion",
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
