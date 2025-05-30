<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
class PreAssembled extends Model
{
    use Auditable;

    protected $fillable = [
        'code',
        'description',
        'parent_description',
        'activity',
    ];

    public function articles()
    {
        return $this->belongsToMany(Article::class)->withPivot('order')->withTimestamps();
    }

    public function lottos()
    {
        return $this->hasMany(Lotto::class);
    }
}
