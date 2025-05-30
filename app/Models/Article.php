<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
class Article extends Model
{
    use Auditable;

    protected $fillable = [
        'code',
        'description',
        'is_moka',
    ];

    public function preassemblies()
    {
        return $this->belongsToMany(Preassembly::class)->withPivot('order')->withTimestamps();
    }

    public function lottos()
    {
        return $this->belongsToMany(Lotto::class)->withTimestamps();
    }
}
