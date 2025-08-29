<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Models\PreAssembled;
use App\Models\Article;
use App\Models\LottoArticle;
class Lotto extends Model
{
    use Auditable;

    protected $fillable = [
        'code_lotto',
        'pre_assembled_id',
        'quantity',
    ];

    public function preAssembled()
    {
        return $this->belongsTo(PreAssembled::class, 'pre_assembled_id');
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'lotto_articles')->withPivot('supplier_code')->withTimestamps();
    }

    public function lottoArticles()
    {
        return $this->hasMany(LottoArticle::class);
    }
}
