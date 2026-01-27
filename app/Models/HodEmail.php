<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HodEmail extends Model
{	
	protected $table = 'hod_emails'; 
    protected $fillable = ['hod_id','type','email'];

    public function hod()
    {
        return $this->belongsTo(Hod::class, 'hod_id', 'id');
    }
}
