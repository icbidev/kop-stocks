<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Route;
use App\Models\Role;
class RoleRoute extends Model
{
    protected $table = 'role_route';
    protected $fillable = ['role_id', 'route_id'];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    
    public function routes()
    {
        return $this->belongsToMany(Route::class);
    }
    
}
