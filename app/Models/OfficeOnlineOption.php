<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeOnlineOption extends Model
{
    protected $table = 'office_online_options';

    protected $fillable = [
        'office_online_question_id',
        'option_text',
        'is_correct'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function question()
    {
        return $this->belongsTo(OfficeOnlineQuestion::class, 'office_online_question_id');
    }
}