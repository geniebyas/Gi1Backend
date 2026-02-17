<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobMst extends Model
{
    protected $table = 'jobs_mst';
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
    public function applications() { return $this->hasMany(JobApplication::class, 'job_id', 'id'); }
}
