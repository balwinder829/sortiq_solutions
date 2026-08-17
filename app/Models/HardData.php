<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HardData extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hard_data';

    protected $primaryKey = 'id';

    protected $fillable = [
        'session_id',
        'college_id',
        'college_name',
        'student_name',
        'student_email',
        'student_mobile',
        'class',
        'semester',
        'course_type',
        'source',
        'is_moved_to_enquiry',
        'gender',
        'enquiry_status',
        'closed_reason',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'session_id'   => 'integer',
        'college_id'   => 'integer',
        'semester'     => 'integer',
         'closed_at'    => 'datetime',
        'closed_by'    => 'integer',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    // Optional: default values
    protected $attributes = [
        'source' => 'manual',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships (Optional)
    |--------------------------------------------------------------------------
    */

    public function session()
    {
        return $this->belongsTo(StudentSession::class);
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function allSession()
    {
        return $this->belongsTo(StudentSession::class, 'session_id')
            ->withoutGlobalScopes();
    }
}