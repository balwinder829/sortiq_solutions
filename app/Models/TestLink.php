<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestLink extends Model
{
    protected $fillable = [
        'test_id',
        'college_id',
        'slug',
        'valid_from',
        'valid_until',
        'is_enabled',
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }
}