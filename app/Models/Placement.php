<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Placement extends Model
{
    use HasFactory;

    protected $table = 'placements';

    protected $fillable = [
        'student_name',
        'tech',
        'placement_date',
        'college_name',
        'phone_no',
        'address',
        'company',
        'description',
        'cover_image',
        'session_id',
        'state_id',
        'location',
    ];

    /* -----------------------------
       Relationships
    ----------------------------- */
    public function images()
    {
        return $this->hasMany(PlacementImage::class);
    }

    public function videos()
    {
        return $this->hasMany(PlacementVideo::class);
    }

    public function college()
    {
        return $this->belongsTo(College::class,'college_name','id');
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function session()
    {
        return $this->belongsTo(StudentSession::class, 'session_id');
    }

    public function companyRelation()
    {
        return $this->belongsTo(PlacementCompany::class, 'company', 'id');
    }

     public function course()
    {
        return $this->belongsTo(Course::class, 'tech', 'id');
    }


    // app/Models/Placement.php
    public function getCollegeFullNameAttribute()
    {
        $college  = $this->college;

        if (!$college) {
            return '';
        }

        $parts = [
            $college->college_name ?? '',
            $college->district->name ?? '',
            $college->state->name ?? '',
        ];

        return implode(', ', array_filter($parts));
    }



}
