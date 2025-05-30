<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class MocaArticle extends Model
{
    use Auditable;

    protected $fillable = [
        'code',
        'description',
    ];

    public function mokaPreassemblies()
    {
        return $this->belongsToMany(MokaPreassembly::class)->withPivot('order')->withTimestamps();
    }
}
