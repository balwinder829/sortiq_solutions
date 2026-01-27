<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSession extends Model
{
    protected $table = 'sessions';

    // Relationship: Session → Batches
    public function batches()
    {
        return $this->hasMany(Batch::class, 'session_name', 'id');
    }
}
