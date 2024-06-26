<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FeedbackQuestionCategory;
use App\Models\FeedbackQuestions;
use App\Models\FeedbackUsersResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function getAllFeedback(Request $request)
    {
        $categories = FeedbackQuestionCategory::where('status',true)->with('questions')->get();
        // $categories = FeedbackQuestionCategory::where('status',true)->with('questions')->inRandomOrder()->get();

        foreach ($categories as $category) {
            foreach ($category->questions as $q) {
                $q->issubmitted = FeedbackUsersResponse::where('question_id', $q->question_id)->where('uid', $request->header('uid'))->exists();
            }
        }
        

        return response()->json([
            "message" => "Categories Loaded Successfully",
            "status" => 1,
            "data" => $categories
        ], 200);
    }

    public function submitFeedbackAnswer(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'question_id' => 'required|exists:feedback_questions,question_id',
            'response_text' => 'nullable|string',
            'response_boolean' => 'nullable|boolean',
            'response_range' => 'nullable',
            'response_choice' => 'nullable',
            'response_date' => 'nullable' // Adjust choices as per your actual options
        ]);

        // Create or update the user's response
        $response = FeedbackUsersResponse::updateOrCreate(
            [
                'uid' => $request->header('uid'),
                'question_id' => $request->question_id,
            ],
            [
                'response_text' => $request->response_text,
                'response_boolean' => $request->response_boolean,
                'response_range' => $request->response_range,
                'response_choice' => $request->response_choice,
                'response_date' => $request->response_date,
            ]
        );
        $question = FeedbackQuestions::find($request->question_id);

        $resp = addCoins($request->header('uid'),3,"You received coins for answering question : <b>$question->question_text</b>");

        return response()->json(['message' => 'Feedback answer submitted successfully', 'status' => 1, 'data' => $response,'coin_data' =>$resp],200);
    }

    public function getCategory(Request $request, $id)
    {
        $category = FeedbackQuestionCategory::with('questions')->find($id);

        foreach ($category->questions as $q) {
            $q->issubmitted = FeedbackUsersResponse::where('question_id', $q->question_id)->where('uid', $request->header('uid'))->exists();
        }

        if ($category != null) {
            return response()->json([
                "message" => "Categories Loaded Successfully",
                "status" => 1,
                "data" => $category
            ], 200);
        } else {
            return response()->json([
                "message" => "No Category Found!",
                "status" => 0,
                "data" => "no data found!"
            ], 204);
        }
    }
}
