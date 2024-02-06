<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    use HasFactory;

    protected $table = 'industries';

    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'description',
        'thumbnail',
        'file',
        'type',
        'ispinned',
        'status',
        'pinnedthumb'
    ];

    protected $casts = [
        'ispinned' => 'boolean',
        'status' => 'boolean',
    ];
}
