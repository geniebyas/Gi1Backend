<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FitnessGym extends Model
{
    protected $table = 'fitness_gyms';
    protected $fillable = [
            'name',
            'description',
            'img_url',
            'status',
            'created_by',
            'location',
            'phone',
            'email',
            'website',
            'instagram',
            'rating',
            'views',
        ];

        public function user()
    {        return $this->belongsTo(User::class, 'created_by', 'uid');
    }
}
