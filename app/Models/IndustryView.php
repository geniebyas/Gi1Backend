<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndustryView extends Model
{
    use HasFactory;

    protected $table = "industry_views";
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'industry_id',
        'uid',
        'created_at',
        'updated_at'
    ];

    public function user(){
        return $this->belongsTo(User::class,'uid');
    }

    public function industry(){
        return $this->belongsTo(Industry::class,'id','industry_id');
    }

}
