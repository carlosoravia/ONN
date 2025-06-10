<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
class LottoArticle extends Model
{
    use Auditable;
    protected $fillable = [
        'lotto_id',
        'article_id',
        'supplier_code',
    ];
}
