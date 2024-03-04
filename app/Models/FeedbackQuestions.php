<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackQuestions extends Model
{
    protected $primaryKey = 'question_id';

    protected $fillable = [
        'category_id',
        'question_text',
        'answer_type',
        'answers'
        // Add other fillable fields as needed
    ];

    protected $casts = [
        // Add casts for specific fields if needed
    ];

    // Define other properties and methods as needed

    public function category()
    {
        return $this->belongsTo(FeedbackQuestionCategory::class, 'category_id');
    }

    public function responses()
    {
        return $this->hasMany(FeedbackUsersResponse::class, 'question_id');
    }
}
