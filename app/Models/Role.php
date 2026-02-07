<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;
// class Role extends Model
// {
//     protected $fillable = ['name', 'description'];

//     public function users() {
//         return $this->belongsToMany(User::class);
//     }
// }
class Role extends SpatieRole
{
    protected $fillable = ['name', 'guard_name', 'description'];
}
