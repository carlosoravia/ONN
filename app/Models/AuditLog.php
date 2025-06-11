<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'table_name',
        'record_id',
        'changed_data',
    ];

    protected $casts = [
        'changed_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTargetRecord()
{
    $map = [
        'lotti' => \App\Models\Lotto::class,
        'users' => \App\Models\User::class,
        'pre_assemblati' => \App\Models\PreAssemblato::class,
        // aggiungi altri se necessario
    ];

    if (!isset($map[$this->table_name])) {
        return null;
    }

    return $map[$this->table_name]::find($this->record_id);
}
}
