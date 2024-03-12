<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackUsersResponse extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'feedback_users_response';

    protected $fillable = [
        'uid',
        'question_id',
        'response_text',
        'response_boolean',
        'response_range',
        'response_date',
        'response_choice'
        // Add other fillable fields as needed
    ];

    protected $casts = [
        // Add casts for specific fields if needed
    ];

    // Define other properties and methods as needed

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function question()
    {
        return $this->belongsTo(FeedbackQuestions::class, 'question_id');
    }
}
