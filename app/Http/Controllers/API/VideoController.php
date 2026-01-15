<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoComment;
use App\Models\VideoLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function addVideo(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
        ]);

        $user = User::where('uid',$request->header('uid'))->first();

        // Create a new video record
        $video = Video::create([
            'title' => $request->title,
            'url' => $request->url,
            'thumbnail' => $request->file('thumbnail') ? $request->file('thumbnail')->store('thumbnails', 'public') : null,
            'description' => $request->description,
            'is_featured' => $request->is_featured ?? false,
            'uid' => $user->uid,
            'is_active' => true,
        ]);


        return response()->json([
            'message' => 'Video added successfully',
            'data' => $video,
            'true' => true,
        ]);
    }
    public function getVideos()
    {
        $videos = Video::with(['user', 'comments.user', 'likes.user', 'saves'])->where('is_active', true)->get();
        return response()->json([
            'message' => 'Videos retrieved successfully',
            'data' => $videos,
            'status' => 1,
        ]);
    }
    public function getVideo(Request $request, $id)
    {
        $video = Video::with(['user', 'comments.user', 'likes.user', 'saves'])->where('id', $id)->first();
        if ($video != null) {
            $video->views = $video->views + 1;
            $video->update();
            return response()->json([
                'message' => 'Video retrieved successfully',
                'data' => $video,
                'status' => 1,
            ]);
        } else {
            return response()->json([
                'message' => 'Video not found',
                'data' => null,
                'status' => 0,
            ]);
        }
    }
    public function updateVideo(Request $request, $id)
    {
        $video = Video::where('id', $id)->first();
        if ($video != null) {
            // Validate the incoming request data
            $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'url' => 'sometimes|required|url',
                'thumbnail' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg',
                'description' => 'sometimes|nullable|string',
                'is_featured' => 'sometimes|nullable|boolean',
                'is_active' => 'sometimes|nullable|boolean',
            ]);

            // Update video fields if provided
            if ($request->has('title')) {
                $video->title = $request->title;
            }
            if ($request->has('url')) {
                $video->url = $request->url;
            }
            if ($request->hasFile('thumbnail')) {
                if ($video->thumbnail) {
                    // Delete the old thumbnail
                    Storage::disk('public')->delete($video->thumbnail);
                }
                $video->thumbnail = $request->file('thumbnail')->store('thumbnails', 'public');
            }
            if ($request->has('description')) {
                $video->description = $request->description;
            }
            if ($request->has('is_featured')) {
                $video->is_featured = $request->is_featured;
            }
            if ($request->has('is_active')) {
                $video->is_active = $request->is_active;
            }

            $video->update();

            return response()->json([
                'message' => 'Video updated successfully',
                'data' => $video,
                'true' => true,
            ]);
        } else {
            return response()->json([
                'message' => 'Video not found',
                'data' => null,
                'true' => false,
            ]);
        }
    }

    public function deleteVideo(Request $request, $id)
    {
        $video = Video::where('id', $id)->first();
        if ($video != null) {
            $video->is_active = false;
            $video->update();
            return response()->json([
                'message' => 'Video deleted successfully',
                'data' => null,
                'true' => true,
            ]);
        } else {
            return response()->json([
                'message' => 'Video not found',
                'data' => null,
                'true' => false,
            ]);
        }
    }
    public function toggleLike(Request $request, $id)
    {
        $video = Video::where('id', $id)->first();
        if ($video != null) {
            $user = User::where('uid',$request->header('uid'))->first();
            $like = VideoLike::where('video_id', $id)->where('uid', $user->uid)->first();
            if ($like != null) {
                $like->is_liked = !$like->is_liked;
                $like->update();
            } else {
                $like = VideoLike::create([
                    'video_id' => $id,
                    'uid' => $user->uid,
                    'is_liked' => true,
                ]);
            }
            return response()->json([
                'message' => 'Video like status toggled successfully',
                'data' => $like,
                'status' => 1,
            ]);
        } else {
            return response()->json([
                'message' => 'Video not found',
                'data' => null,
                'status' => 0,
            ]);
        }
        }
    public function loadAnalytics(Request $request, $id)
    {
        $video = Video::where('id', $id)->first();
        if ($video != null) {
            $likesCount = VideoLike::where('video_id', $id)->where('is_liked', true)->count();
            $commentsCount = VideoComment::where('video_id', $id)->count();
            return response()->json([
                'message' => 'Video analytics retrieved successfully',
                'data' => [
                    'views' => $video->views,
                    'likes' => $likesCount,
                    'comments' => $commentsCount,
                ],
                'true' => true,
            ]);
        } else {
            return response()->json([
                'message' => 'Video not found',
                'data' => null,
                'true' => false,
            ]);
        }
    }
    public function addComment(Request $request, $id)
    {
        $video = Video::where('id', $id)->first();
        if ($video != null) {
            // Validate the incoming request data
            $request->validate([
                'comment' => 'required|string',
            ]);

            $user = User::where('uid',$request->header('uid'))->first();

            // Create a new comment
            $comment = VideoComment::create([
                'video_id' => $id,
                'uid' => $user->uid,
                'comment' => $request->comment,
            ]);

            return response()->json([
                'message' => 'Comment added successfully',
                'data' => $comment,
                'status' => 1,
            ]);
        } else {
            return response()->json([
                'message' => 'Video not found',
                'data' => null,
                'status' => 0,
            ]);
        }
    }
    public function saveVideo(Request $request, $id)
    {
        $video = Video::where('id', $id)->first();
        if ($video != null) {
            $user = User::where('uid',$request->header('uid'))->first();
            // Check if the video is already saved
            $existingSave = $user->videoSaves()->where('video_id', $id)->first();
            if ($existingSave) {
                $existingSave->delete();
                return response()->json([
                    'message' => 'Video removed from saved videos',
                    'data' => null,
                    'true' => true,
                ]);
            }
            $save = $user->videoSaves()->create([
                'video_id' => $id,
            ]);
            return response()->json([
                'message' => 'Video saved successfully',
                'data' => $save,
                'status' => 1,
            ]);
        } else {
            return response()->json([
                'message' => 'Video not found',
                'data' => null,
                'status' => 0,
            ]);
        }
    }


}
