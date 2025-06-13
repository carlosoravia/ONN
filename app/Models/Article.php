<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Models\Preassembled;
class Article extends Model
{
    use Auditable;

    protected $fillable = [
        'code',
        'description',
        'is_moca',
    ];

    public function preassemblies()
    {
        return $this->belongsToMany(PreAssembled::class, 'preassembled_articles')
            ->withPivot('order')->withTimestamps();
    }

    public function lottos()
    {
        return $this->belongsToMany(Lotto::class, 'lotto_articles')->withPivot('supplier_code')->withTimestamps();
    }
}
