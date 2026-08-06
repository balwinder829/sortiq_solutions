<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enquiry extends Model
{
    use SoftDeletes;

    protected $casts = [
        'assigned_at'       => 'datetime',
        'last_contacted_at' => 'datetime',
        'next_followup_at'  => 'datetime', // 🔥 MISSING
        'registered_at'     => 'datetime',
        'closed_at'         => 'datetime',
    ];

    protected $guarded = [];

    protected static function booted()
    {
        static::deleting(function ($enquiry) {

            if ($enquiry->isForceDeleting()) {
                return;
            }

            // ✅ ONLY enquiry-owned tables
            $enquiry->followups()->delete();
            $enquiry->activities()->delete();
            $enquiry->registration()->delete();
            $enquiry->movements()->delete();
        });

        static::restoring(function ($enquiry) {

            $enquiry->followups()->withTrashed()->restore();
            $enquiry->activities()->withTrashed()->restore();
            $enquiry->registration()->withTrashed()->restore();
            $enquiry->movements()->withTrashed()->restore();
        });
    }

    public function followups()
    {
        return $this->hasMany(EnquiryFollowup::class);
    }

    public function activities()
    {
        return $this->hasMany(EnquiryActivity::class);
    }

    public function registration()
    {
        return $this->hasOne(Registration::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(SalesStaff::class, 'assigned_to');
    }

     public function student()
    {
        return $this->hasOne(Student::class, 'enquiry_id');
    }

    public function collegeData()
    {
        return $this->belongsTo(College::class,'college','id');
    }

    public function scopeEnquiries($query)
    {
        return $query->where('is_passout', 0);
    }

    public function scopePassouts($query)
    {
        return $query->where('is_passout', 1);
    }
    public function salesStaff()
    {
        return $this->belongsTo(SalesStaff::class, 'assigned_to');
    }

    public function movements()
    {
        return $this->hasMany(EnquiryMovement::class)
            ->latest();
    }

    public function currentSession()
    {
        return $this->belongsTo(StudentSession::class, 'session_id');
    }

    public function scopeActive($query)
    {
        return $query->where('enquiry_status', 'active');
    }

    public function scopeClosed($query)
    {
        return $query->where('enquiry_status', 'closed');
    }
    public function scopeAdmitted($query)
    {
        return $query->where('enquiry_status', 'admitted');
    }
}
