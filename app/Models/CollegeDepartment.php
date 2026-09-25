<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CollegeDepartment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'college_departments';

    protected $fillable = [
        'name',
        'type',
        'short_name',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Scope: Active departments only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope: Degree departments
     */
    public function scopeDegree($query)
    {
        return $query->where('type', 'degree');
    }

    /**
     * Scope: Diploma departments
     */
    public function scopeDiploma($query)
    {
        return $query->where('type', 'diploma');
    }
}