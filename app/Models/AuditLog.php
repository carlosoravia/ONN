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
            'lottos' => \App\Models\Lotto::class,
            'users' => \App\Models\User::class,
            'pre_assembleds' => \App\Models\Preassembled::class,
            'lotto_articles' => \App\Models\LottoArticle::class,
            // aggiungi altri se necessario
        ];

        if (!isset($map[$this->table_name])) {
            return null;
        }

        return $map[$this->table_name]::find($this->record_id);
    }

    public function getRecordUrl()
    {
        $routes = [
            'lotto_articles' => function ($id) {
                $pivot = \App\Models\LottoArticle::find($id);
                return $pivot && $pivot->lotto_id
                    ? route('lotto.edit', $pivot->lotto_id)
                    : null;
            },

            'lottos' => fn($id) => route('lotto.edit', $id),
            //'users' => fn($id) => route('users.delete', $id),
            // 'pre_assembleds' => fn($id) => route('pre_assembleds.show', $id),
        ];

        $table = $this->table_name;

        return isset($routes[$table])
            ? $routes[$table]($this->record_id)
            : null;
    }
}
