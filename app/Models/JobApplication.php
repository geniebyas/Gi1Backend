<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $table = 'jobs_applications';
    protected $fillable = [
        'id',
        'job_id',
        'applicant_name',
        'applicant_email',
        'applicant_phone',
        'cover_letter',
        'resume',
        'created_by',
        'received_by',
        'status'
        ];


    public function job()
    {
        return $this->belongsTo(JobMst::class, 'job_id', 'id');
    }
    public function applicant()
    {
        return $this->belongsTo(User::class, 'created_by', 'uid');
    }
    public function recruiter()
    {
        return $this->belongsTo(User::class, 'received_by', 'uid');
    }
}
