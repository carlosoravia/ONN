<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Models\PreAssembled;
class Article extends Model
{
    use Auditable;

    protected $fillable = [
        'code',
        'description',
        'padre_description',
        'is_moca',
    ];

    public function preassembleds()
    {
        return $this->belongsToMany(PreAssembled::class, 'preassembled_articles')
            ->withPivot('order')->withTimestamps();
    }

    public function lottos()
    {
        return $this->belongsToMany(Lotto::class, 'lotto_articles')->withPivot('supplier_code')->withTimestamps();
    }
}
