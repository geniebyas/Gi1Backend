<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FeedbackQuestionCategory;
use App\Models\FeedbackUsersResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function getAllFeedback()
        {
            $categories = FeedbackQuestionCategory::with('questions')->get();
    
            return response()->json([
                "message" => "Categories Loaded Successfully",
                "status" => 1,
                "data" => $categories
            ],200);
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
                    'response_date'=>$request->response_date,
                ]
            );
    
            return response()->json(['message' => 'Feedback answer submitted successfully', 'status' => 1,'data' => $response]);
        }
    

}
