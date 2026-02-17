<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobMst extends Model
{
    protected $table = 'job_mst';
    protected $fillable = [
        'id',
        'name',
        'description',
        'experience',
        'salary',
        'skills',
        'location',
        'company',
        'website',
        'banner',
        'type',
        'status',
        'created_by'
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by', 'uid'); }
}
