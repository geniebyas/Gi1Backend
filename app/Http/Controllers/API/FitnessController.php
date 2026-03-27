<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FitnessCategory;
use App\Models\FitnessGym;
use App\Models\FitnessTip;
use App\Models\FitnessVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FitnessController extends Controller
{
    //create fitness category
    public function createFitnessCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'status' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ]);
        }

        $fitnessCategory = new FitnessCategory();
        $fitnessCategory->name = $request->name;
        $fitnessCategory->description = $request->description;
        $fitnessCategory->img_url = $request->img ? $request->file('img')->store('fitness_categories', 'public') : null;
        $fitnessCategory->status = $request->status;
        $fitnessCategory->created_by = $request->header('uid');
        $fitnessCategory->save();

        return response()->json([
            'status' => 1,
            'message' => 'Fitness category created successfully',
            'data' => $fitnessCategory
        ]);
    }
    //get all fitness categories
    public function getFitnessCategories(Request $request)
    {
        if ($request->has('status')) {
            $fitnessCategories = FitnessCategory::where('status', $request->status)->get();
        } else {
            $fitnessCategories = FitnessCategory::all();
        }

        return response()->json([
            'status' => 1,
            'message' => 'Fitness categories retrieved successfully',
            'data' => $fitnessCategories
        ]);
    }
    //get fitness category by id
    public function getFitnessCategoryById($id)
    {
        $fitnessCategory = FitnessCategory::find($id);

        if (!$fitnessCategory) {
            return response()->json([
                'status' => 0,
                'message' => 'Fitness category not found',
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Fitness category retrieved successfully',
            'data' => $fitnessCategory
        ]);
    }
    //update fitness category
    public function updateFitnessCategory(Request $request, $id)
    {
        $fitnessCategory = FitnessCategory::find($id);

        if (!$fitnessCategory) {
            return response()->json([
                'status' => 0,
                'message' => 'Fitness category not found',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'status' => 'required|string',
            'created_by' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ]);
        }

        $fitnessCategory->name = $request->name;
        $fitnessCategory->description = $request->description;

        $fitnessCategory->img_url = $request->img ? $request->file('img')->store('fitness_categories', 'public') : $fitnessCategory->img_url;
        if ($request->hasFile('img')) {
            // Delete the old image if it exists
            if ($fitnessCategory->img_url) {
                Storage::disk('public')->delete($fitnessCategory->img_url);
            }
            $fitnessCategory->img_url = $request->file('img')->store('fitness_categories', 'public');
        }
        $fitnessCategory->status = $request->status;
        $fitnessCategory->created_by = $request->created_by;
        $fitnessCategory->save();

        return response()->json([
            'status' => 1,
            'message' => 'Fitness category updated successfully',
            'data' => $fitnessCategory
        ]);
    }
    //soft delete fitness category
    public function toggleFitnessCategory($id)
    {
        $fitnessCategory = FitnessCategory::find($id);
        if (!$fitnessCategory) {
            return response()->json([
                'status' => 0,
                'message' => 'Fitness category not found',
            ]);
        }
        $fitnessCategory->status = $fitnessCategory->status === 'active' ? 'inactive' : 'active';
        $fitnessCategory->save();
        return response()->json([
            'status' => 1,
            'message' => 'Fitness category toggled successfully',
        ]);
    }

    //create fitness video
    public function createFitnessVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'video_url' => 'required|url',
            'status' => 'required|string',
            'category_id' => 'required|string',
            'tags' => 'nullable|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ]);
        }
        $fitnessVideo = new FitnessVideo();
        $fitnessVideo->title = $request->title;
        $fitnessVideo->description = $request->description;
        $fitnessVideo->video_url = $request->video_url;
        $fitnessVideo->status = $request->status;
        $fitnessVideo->created_by = $request->header('uid');
        $fitnessVideo->category_id = $request->category_id;
        $fitnessVideo->tags = $request->tags;
        $fitnessVideo->img_url = $request->img ? $request->file('img')->store('fitness_videos', 'public') : null;
        $fitnessVideo->save();
        return response()->json([
            'status' => 1,
            'message' => 'Fitness video created successfully',
            'data' => $fitnessVideo
        ]);
    }
    //get all fitness videos status and category filter
    public function getFitnessVideos(Request $request)
    {
        $query = FitnessVideo::query();
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        $fitnessVideos = $query->with(['user', 'category', 'likes'])->get();
        return response()->json([
            'status' => 1,
            'message' => 'Fitness videos retrieved successfully',
            'data' => $fitnessVideos
        ]);
    }
    //get fitness video by id
    public function getFitnessVideoById($id)
    {
        $fitnessVideo = FitnessVideo::find($id);
        if (!$fitnessVideo) {
            return response()->json([
                'status' => 0,
                'message' => 'Fitness video not found',
            ]);
        }
        // Increment views count
        $fitnessVideo->increment('views');
        return response()->json([
            'status' => 1,
            'message' => 'Fitness video retrieved successfully',
            'data' => $fitnessVideo
        ]);
    }
    //update fitness video
    public function updateFitnessVideo(Request $request, $id)
    {
        $fitnessVideo = FitnessVideo::find($id);
        if (!$fitnessVideo) {
            return response()->json([
                'status' => 0,
                'message' => 'Fitness video not found',
            ]);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'video_url' => 'required|url',
            'status' => 'required|string',
            'category_id' => 'required|string',
            'tags' => 'nullable|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ]);
        }
        $fitnessVideo->title = $request->title;
        $fitnessVideo->description = $request->description;
        $fitnessVideo->video_url = $request->video_url;
        $fitnessVideo->status = $request->status;
        $fitnessVideo->created_by = $request->header('uid');;
        $fitnessVideo->category_id = $request->category_id;
        $fitnessVideo->tags = $request->tags;
        if ($request->hasFile('img')) {
            // Delete the old image if it exists
            if ($fitnessVideo->img_url) {
                Storage::disk('public')->delete($fitnessVideo->img_url);
            }
            $fitnessVideo->img_url = $request->file('img')->store('fitness_videos', 'public');
        }
        $fitnessVideo->save();
        return response()->json([
            'status' => 1,
            'message' => 'Fitness video updated successfully',
            'data' => $fitnessVideo
        ]);
    }
    //soft delete fitness video
    public function toggleFitnessVideo($id)
    {
        $fitnessVideo = FitnessVideo::find($id);
        if (!$fitnessVideo) {
            return response()->json([
                'status' => 0,
                'message' => 'Fitness video not found',
            ]);
        }
        $fitnessVideo->status = $fitnessVideo->status === 'active' ? 'inactive' : 'active';
        $fitnessVideo->save();
        return response()->json([
            'status' => 1,
            'message' => 'Fitness video toggled successfully',
        ]);
    }
    //create fitness tip
    public function createFitnessTip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'status' => 'required|string',
            'category_id' => 'required|string',
            'tags' => 'nullable|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ]);
        }
        $fitnessTip = new FitnessTip();
        $fitnessTip->title = $request->title;
        $fitnessTip->description = $request->description;
        $fitnessTip->status = $request->status;
        $fitnessTip->created_by = $request->header('uid');
        $fitnessTip->category_id = $request->category_id;
        $fitnessTip->tags = $request->tags;
        $fitnessTip->img_url = $request->img ? $request->file('img')->store('fitness_tips', 'public') : null;
        $fitnessTip->save();
        return response()->json([
            'status' => 1,
            'message' => 'Fitness tip created successfully',
            'data' => $fitnessTip
        ]);
    }
    //get all fitness tips
    public function getFitnessTips(Request $request)
    {

        $query = FitnessTip::query();
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        $fitnessTips = $query->with(['user', 'category'])->get();
        return response()->json([
            'status' => 1,
            'message' => 'Fitness tips retrieved successfully',
            'data' => $fitnessTips
        ]);
    }
    //get fitness tip by id
    public function getFitnessTipById($id)
    {
        $fitnessTip = FitnessTip::find($id);
        if (!$fitnessTip) {
            return response()->json([
                'status' => 0,
                'message' => 'Fitness tip not found',
            ]);
        }
        // Increment views count
        $fitnessTip->increment('views');
        return response()->json([
            'status' => 1,
            'message' => 'Fitness tip retrieved successfully',
            'data' => $fitnessTip
        ]);
    }
    //update fitness tip
    public function updateFitnessTip(Request $request, $id)
    {
        $fitnessTip = FitnessTip::find($id);
        if (!$fitnessTip) {
            return response()->json([
                'status' => 0,
                'message' => 'Fitness tip not found',
            ]);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'status' => 'required|string',
            'category_id' => 'required|string',
            'tags' => 'nullable|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' =>
                $validator->errors()->first(),
                'errors' => $validator->errors()
            ]);
        }
        $fitnessTip->title = $request->title;
        $fitnessTip->description = $request->description;
        $fitnessTip->status = $request->status;
        $fitnessTip->created_by = $request->header('uid');
        $fitnessTip->category_id = $request->category_id;
        $fitnessTip->tags = $request->tags;
        if ($request->hasFile('img')) {
            // Delete the old image if it exists
            if ($fitnessTip->img_url) {
                Storage::disk('public')->delete($fitnessTip->img_url);
            }
            $fitnessTip->img_url = $request->file('img')->store('fitness_tips', 'public');
        }
        $fitnessTip->save();
        return response()->json([
            'status' => 1,
            'message' => 'Fitness tip updated successfully',
            'data' => $fitnessTip
        ]);
    }
    //soft delete fitness tip
    public function toggleFitnessTip($id)
    {
        $fitnessTip = FitnessTip::find($id);
        if (!$fitnessTip) {
            return response()->json([
                'status' => 0,
                'message' => 'Fitness tip not found',
            ]);
        }
        $fitnessTip->status = $fitnessTip->status === 'active' ? 'inactive' : 'active';

        $fitnessTip->save();
        return response()->json([
            'status' => 1,
            'message' => 'Fitness tip toggled successfully',
        ]);
    }

    //toggle fitness video like
    public function toggleFitnessVideoLike(Request $request, $id)
    {
        $fitnessVideo = FitnessVideo::find($id);
        if (!$fitnessVideo) {
            return response()->json([
                'status' => 0,
                'message' => 'Fitness video not found',
            ]);
        }
        $userId = $request->header('uid');
        $like = $fitnessVideo->likes()->where('user_id', $userId)->first();
        if ($like) {
            $like->delete();
            return response()->json([
                'status' => 1,
                'message' => 'Fitness video like removed successfully',
            ]);
        } else {
            $fitnessVideo->likes()->create(['user_id' => $userId]);
            return response()->json([
                'status' => 1,
                'message' => 'Fitness video like added successfully',
            ]);
        }
    }

    //gym create
    public function createFitnessGym(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'location' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'instagram' => 'nullable|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'status' => 'required|string',
            'rating' => 'nullable|integer',
            'views' => 'nullable|integer',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ]);
        }
        $fitnessGym = new FitnessGym();
        $fitnessGym->name = $request->name;
        $fitnessGym->description = $request->description;
        $fitnessGym->location = $request->location;
        $fitnessGym->phone = $request->phone;
        $fitnessGym->email = $request->email;
        $fitnessGym->website = $request->website;
        $fitnessGym->instagram = $request->instagram;
        $fitnessGym->status = $request->status;
        $fitnessGym->created_by = $request->header('uid');
        $fitnessGym->img_url = $request->img ? $request->file('img')->store('fitness_gyms', 'public') : null;
        $fitnessGym->rating = $request->rating ?? 0;
        $fitnessGym->views = $request->views ?? 0;
        $fitnessGym->save();
        return response()->json([
            'status' => 1,
            'message' => 'Fitness gym created successfully',
            'data' => $fitnessGym
        ]);
    }
    //get all fitness gyms with status filter
    public function getFitnessGyms(Request $request)
    {
        $query = FitnessGym::query();
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        $fitnessGyms = $query->get();
        return response()->json([
            'status' => 1,
            'message' => 'Fitness gyms retrieved successfully',
            'data' => $fitnessGyms
        ]);
    }
    //get fitness gym by id
    public function getFitnessGymById($id)
    {
        $fitnessGym = FitnessGym::find($id);
        if (!$fitnessGym) {
            return response()->json([
                'status' => 0,
                'message' => 'Fitness gym not found',
            ]);
        }
        // Increment views count
        $fitnessGym->increment('views');
        return response()->json([
            'status' => 1,
            'message' => 'Fitness gym retrieved successfully',
            'data' => $fitnessGym
        ]);
    }
    //update fitness gym
    public function updateFitnessGym(Request $request, $id)
    {
        $fitnessGym = FitnessGym::find($id);
        if (!$fitnessGym) {
            return response()->json([
                'status' => 0,
                'message' => 'Fitness gym not found',
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'location' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'instagram' => 'nullable|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'status' => 'required|string',
            'rating' => 'nullable|integer',
            'views' => 'nullable|integer',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ]);
        }
        $fitnessGym->name = $request->name;
        $fitnessGym->description = $request->description;
        $fitnessGym->location = $request->location;
        $fitnessGym->phone = $request->phone;
        $fitnessGym->email = $request->email;
        $fitnessGym->website = $request->website;
        $fitnessGym->instagram = $request->instagram;
        $fitnessGym->status = $request->status;
        if ($request->hasFile('img')) {
            // Delete the old image if it exists
            if ($fitnessGym->img_url) {
                Storage::disk('public')->delete($fitnessGym->img_url);
            }
            $fitnessGym->img_url = $request->file('img')->store('fitness_gyms', 'public');
        }
        $fitnessGym->rating = $request->rating ?? 0;
        $fitnessGym->views = $request->views ?? 0;
        $fitnessGym->save();
        return response()->json([
            'status' => 1,
            'message' => 'Fitness gym updated successfully',
            'data' => $fitnessGym
        ]);
    }
    //soft delete fitness gym
    public function toggleFitnessGym($id)
    {
        $fitnessGym = FitnessGym::find($id);
        if (!$fitnessGym) {
            return response()->json([
                'status' => 0,
                'message' => 'Fitness gym not found',
            ]);
        }
        $fitnessGym->status = $fitnessGym->status === 'active' ? 'inactive' : 'active';
        $fitnessGym->save();
        return response()->json([
            'status' => 1,
            'message' => 'Fitness gym toggled successfully',
        ]);
    }
}
