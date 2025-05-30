<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        foreach (['created', 'updated', 'deleted'] as $event) {
            static::$event(function ($model) use ($event) {
                AuditLog::create([
                    'user_id'     => Auth::id() ?? 0,
                    'action'      => $event,
                    'table_name'  => $model->getTable(),
                    'record_id'   => $model->getKey(),
                    'changed_data'=> $event === 'updated' ? json_encode($model->getDirty()) : null,
                ]);
            });
        }
    }
}
