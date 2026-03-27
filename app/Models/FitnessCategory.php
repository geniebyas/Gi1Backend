<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FitnessCategory extends Model
{

    protected $table = 'fitness_categories';
    protected $fillable = [
            'name',
            'description',
            'img_url',
            'status',
            'created_by',
        ];

        public function user()
    {        return $this->belongsTo(User::class, 'created_by', 'uid');
    }
    
}
