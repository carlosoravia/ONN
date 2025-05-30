<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
class Lotto extends Model
{
    use Auditable;

    protected $fillable = [
        'lotto_code',
        'preassembly_id',
        'quantity',
    ];

    public function preassembly()
    {
        return $this->belongsTo(Preassembly::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class)->withTimestamps();
    }
}
