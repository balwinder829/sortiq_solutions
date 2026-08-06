<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnquiryMovement extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function fromSession()
    {
        return $this->belongsTo(StudentSession::class,'from_session_id');
    }

    public function toSession()
    {
        return $this->belongsTo(StudentSession::class,'to_session_id');
    }

    public function movedBy()
    {
        return $this->belongsTo(SalesStaff::class,'moved_by');
    }
}
