<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnquiryActivity extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'enquiry_id',
        'user_id',
        'type',
        'old_value',
        'new_value',
        'details'
    ];

    public function enquiry() {
        return $this->belongsTo(Enquiry::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
