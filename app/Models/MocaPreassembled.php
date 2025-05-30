<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
class MocaPreassembled extends Model
{
    use Auditable;

    protected $fillable = [
        'code',
        'description',
        'activity',
    ];

    public function mokaArticles()
    {
        return $this->belongsToMany(MokaArticle::class)->withPivot('order')->withTimestamps();
    }
}
