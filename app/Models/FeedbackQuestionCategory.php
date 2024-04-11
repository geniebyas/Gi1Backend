<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackQuestionCategory extends Model
{
    protected $primaryKey = 'category_id';

    protected $fillable = [
        'category_name',
        'category_desc',
        'status'
        // Add other fillable fields as needed
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    // Define other properties and methods as needed

    public function questions()
    {
        return $this->hasMany(FeedbackQuestions::class, 'category_id');
    }
}
