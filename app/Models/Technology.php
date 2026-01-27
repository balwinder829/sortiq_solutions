<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Technology extends Model
{
    protected $fillable = ['name', 'category', 'is_active'];

    public function questions()
    {
        return $this->hasMany(InterviewQuestion::class);
    }
}
