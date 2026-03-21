<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class College extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'colleges';
    protected $appends = ['full_name'];

    protected $fillable = [
        'college_name',   // original value shown everywhere
        'clean_name',     // cleaned value for duplicate + slug
        'slug',
        'state_id',
        'district_id',
        'college_display_name',
        'college_short_name',
        'college_type',
        'offer_training',
        'training_in_year',
    ];

    const TYPES = [
        0 => 'Degree',
        1 => 'Diploma',
        2 => 'Both',
        3 => 'Unknown'
    ];

    public function getCollegeTypeLabelAttribute()
    {
        return self::TYPES[$this->college_type] ?? 'N/A';
    }

    /**
     * Clean only extra spaces.
     * Do NOT remove or modify commas.
     */
    public static function clean($name)
    {
        // Trim ends
        $name = trim($name);

        // Collapse multiple spaces into one space
        $name = preg_replace('/\s+/', ' ', $name);

        return $name;
    }

    /**
     * Boot: clean names + generate slug before saving.
     */
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::saving(function ($college) {

    //         // Clean only extra spaces (keep commas)
    //         $college->college_name = self::clean($college->college_name);

    //         // Clean_name = cleaned version (same as college_name)
    //         $college->clean_name = $college->college_name;

    //         // Create slug only once or if slug was reset to null
    //         if (empty($college->slug)) {
    //             $college->slug = self::uniqueSlug($college->clean_name);
    //         }
    //     });
    // }

    /**
     * Create unique slug based on cleaned name.
     */
    // public static function uniqueSlug($name)
    // {
    //     $slug = Str::slug($name);
    //     $original = $slug;
    //     $count = 1;

    //     while (self::where('slug', $slug)->withTrashed()->exists()) {
    //         $slug = $original . '-' . $count;
    //         $count++;
    //     }

    //     return $slug;
    // }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    // public function getFullNameAttribute()
    // {
    //     $parts = [
    //         $this->college_name ?? '',
    //         $this->district->name ?? '',
    //         $this->state->name ?? '',
    //     ];

    //     return implode(', ', array_filter($parts));
    // }


    // public function getFullNameAttribute()
    // {
    //     return $this->college_display_name;
    // }

    public function getFullNameAttribute()
    {
        $name = $this->college_display_name ?? $this->college_name;

        if (!empty($this->college_short_name)) {
            $name .= ' (' . strtoupper($this->college_short_name) . ')';
        }

        return $name;
    }


    public function enquiries()
    {
        return $this->hasMany(Enquiry::class,'college');
    }

    // public function hod()
    // {
    //     return $this->hasOne(Hod::class)
    //         ->where('position', 'HOD');
    // }

    public function hod()
    {
        return $this->hasOne(Hod::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'college_name', 'id');
    }

    public function emailRecipients()
    {
        return $this->hasMany(EmailRecipient::class, 'college_id');
    }
}


