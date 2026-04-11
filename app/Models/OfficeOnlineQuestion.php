<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeOnlineQuestion extends Model
{
    protected $table = 'office_online_questions';

    protected $fillable = [
        'office_online_test_id',
        'question',
        'type',
        'marks'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function test()
    {
        return $this->belongsTo(OfficeOnlineTest::class, 'office_online_test_id');
    }

    public function options()
    {
        return $this->hasMany(OfficeOnlineOption::class);
    }
}