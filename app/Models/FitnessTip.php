<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FitnessTip extends Model
{
    protected $table = 'fitness_tips';
    protected $fillable = [
            'title',
            'description',
            'status',
            'created_by',
            'category_id',
            'tags',
            'img_url',
            'views',
        ];

        public function user()
    {        return $this->belongsTo(User::class, 'created_by', 'uid');
    }

    public function category()
    {        return $this->belongsTo(FitnessCategory::class, 'category_id', 'id');
    }
}
