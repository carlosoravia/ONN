<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Models\Preassembled;

class Lotto extends Model
{
    use Auditable;

    protected $fillable = [
        'code_lotto',
        'pre_assembled_id',
        'quantity',
    ];

    public function preassembly()
    {
        return $this->belongsTo(Preassembled::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'lotto-articles')->withPivot('supplier_code')->withTimestamps();
    }
}
