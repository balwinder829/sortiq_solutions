<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'source',
        'assigned_to',
        'status',
        'follow_up_date',
        'notes',
        'created_by', 
        'batch_id', 
    ];

    protected $dates = ['follow_up_date'];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function calls()
    {
        return $this->hasMany(LeadCall::class, 'lead_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activityLogs()
    {
        return $this->hasMany(LeadActivityLog::class, 'lead_id')->latest();
    }

}
