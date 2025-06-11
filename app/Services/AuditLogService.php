<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
class AuditLogService
{
    public static function log(string $azione, string $descrizione, ?Model $modello = null): void
    {
        if (!$modello || !$modello->getTable() || !$modello->getKey()) {
            \Log::debug('[AuditLog] Modello non valido o nullo');
            return;
        }

        $dati = [];

        switch ($azione) {
            case 'created':
                foreach ($modello->getAttributes() as $campo => $valore) {
                    $dati[$campo] = ['old' => null, 'new' => $valore];
                }
                break;

            case 'updated':
                foreach ($modello->getAttributes() as $campo => $valore) {
                    $vecchio = $modello->getOriginal($campo);

                    $old = $vecchio instanceof \Carbon\Carbon ? $vecchio->toDateTimeString() : (string) $vecchio;
                    $new = $valore instanceof \Carbon\Carbon ? $valore->toDateTimeString() : (string) $valore;

                    if ($old !== $new) {
                        $dati[$campo] = ['old' => $vecchio, 'new' => $valore];
                    }
                }
                break;

            case 'deleted':
                foreach ($modello->getAttributes() as $campo => $valore) {
                    $dati[$campo] = ['old' => $valore, 'new' => null];
                }
                break;
        }

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => $azione,
            'table_name'  => $modello->getTable(),
            'record_id'   => $modello->getKey(),
            'changed_data'=> json_encode($dati),
        ]);
    }
}
