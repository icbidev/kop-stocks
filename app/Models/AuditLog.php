<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class AuditLog extends Model
{
    protected $fillable = [
        'model_type',
        'model_id',
        'event',
        'old_values',
        'new_values',
        'user_id'
    ];

    public function user()
{
    return $this->belongsTo(User::class);
}


    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];
}
