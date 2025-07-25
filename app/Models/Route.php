<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Route extends Model
{
    protected $table = 'routes';
    
    protected $fillable = ['name', 'path'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
