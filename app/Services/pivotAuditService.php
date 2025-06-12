<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class PivotAuditService
{
    public static function logChange(
        string $table,
        int $lottoId,
        int $relatedId,
        string $relatedKeyName,
        string $fieldName,
        ?array $old,
        ?array $new
    ): void {
        // dd("tabella -> " . $table,
        // "lotto id -> " . $lottoId,
        // "related id -> " . $relatedId,
        // "related key name-> " . $relatedKeyName,
        // "field name -> " . $fieldName,
        // "old: ", $old,
        // "new: ", $new);
        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => $old === null ? 'created' : 'updated',
            'table_name'  => $table,
            'record_id'   => $lottoId,
            'changed_data'=> json_encode([
                $relatedKeyName => $relatedId,
                $fieldName      => ['old' => $old, 'new' => $new],
            ]),
        ]);
    }
}
