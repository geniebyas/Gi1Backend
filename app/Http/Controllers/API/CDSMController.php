<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CDSMPost as Post;
use App\Models\CDSMPost;
use App\Models\CDSMPostLikes as PostLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CDSMController extends Controller
{
    public function addPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'img' => 'nullable|image',
            'category' => 'required',
            'caption' => 'nullable',
            'location' => 'nullable',
            'description' => 'nullable',
            'tags' => 'nullable'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 0,
                'data' => $validator->errors()
            ]);
        }

        $post = new CDSMPost();
        $post->img = $request->file('img') != null ? $request->file('img')->store('posts','public') : null;
        $post->category = $request->category;
        $post->caption = $request->caption;
        $post->location = $request->location;
        $post->description = $request->description;
        $post->tags = $request->tags;
        $post->uid = $request->header('uid');
        $post->save();
        return response()->json([
            'message' => 'Post Added Successfully',
            'status' => 1,
            'data' => $post
        ]);
    }

    public function getPosts()
    {
        // we want to fetch random 30 posts only
        $posts = CDSMPost::where('is_active', true)
            ->with(['user', 'comments', 'likes', 'interested'])
            ->inRandomOrder()
            ->limit(30)
            ->get();

        return response()->json([
            'message' => 'Posts Loaded Successfully',
            'status' => 1,
            'data' => $posts
        ]);
    }

    public function loadAnalytics($id)
    {
            $post = CDSMPost::with(['user', 'comments', 'likes', 'interested'])->find($id);

        if($post == null){
            return response()->json([
                'message' => 'Post not found',
                'status' => 0,
                'data' => null
            ]);
        }
        $post->views = $post->views + 1;
        $post->update();
        return response()->json([
            'message' => 'Analytics Loaded Successfully',
            'status' => 0,
            'data' => $post
        ]);
    }

    public function toggleLike(Request $request, $id)
    {
        $uid = $request->header('uid');

        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
                'status' => 0,
                'data' => null
            ]);
        }

        // find existing like/dislike record for this user & post
        $like = PostLike::where('post_id', $id)
            ->where('uid', $uid)
            ->first();

        if ($like) {
            // toggle the boolean is_liked field
            $like->is_liked = !$like->is_liked;
            $like->save();

            return response()->json([
                'message' => $like->is_liked ? 'Like Added Successfully' : 'Like Removed Successfully',
                'status' => 1,
                'data' => $like
            ]);
        } else {
            // create a new like (is_liked = true)
            $like = new PostLike();
            $like->post_id = $id;
            $like->uid = $uid;
            $like->is_liked = true;
            $like->save();

            return response()->json([
                'message' => 'Like Added Successfully',
                'status' => 1,
                'data' => $like
            ]);
        }
    }

    public function myPosts(Request $request)
    {
        $uid = $request->header('uid');
        $posts = CDSMPost::where('uid', $uid)
            ->where('is_active', true)
            ->with(['comments', 'likes', 'interested'])->get();
        return response()->json([
            'message' => 'Posts fetched successfully',
            'status' => 1,
            'data' => $posts
        ]);
    }

    public function deletePost(Request $request, $id)
    {
        $post = CDSMPost::find($id);
        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
                'status' => 0,
                'data' => null
            ]);
        }

        $post->is_active = false;
        $post->save();

        return response()->json([
            'message' => 'Post deleted successfully',
            'status' => 1,
            'data' => $post
        ]);
    }



}
