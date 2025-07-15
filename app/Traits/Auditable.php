<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    public static function bootAuditable()
    {
        static::updating(function ($model) {
            $old = $model->getOriginal();
            $new = $model->getDirty();

            // Only log if there are actual changes
            if (!empty($new)) {
                AuditLog::create([
                    'model_type' => get_class($model),
                    'model_id' => $model->getKey(),
                    'event' => 'update',
                    'old_values' => collect($old)->only(array_keys($new)),
                    'new_values' => $new,
                    'user_id' => auth()->id(), // <== Add this
                ]);
            }
        });

        static::created(function ($model) {
            AuditLog::create([
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
                'event' => 'create',
                'old_values' => null,
                'new_values' => $model->getAttributes(),
                'user_id' => auth()->id(), // <== Add this
            ]);
        });

        static::deleted(function ($model) {
            AuditLog::create([
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
                'event' => 'delete',
                'old_values' => $model->getOriginal(),
                'new_values' => null,
                'user_id' => auth()->id(), // <== Add this
            ]);
        });
    }
}
