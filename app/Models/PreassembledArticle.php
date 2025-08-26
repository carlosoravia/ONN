<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class PreassembledArticle extends Model
{
    use Auditable;
    protected $fillable = [
        'pre_assembled_id',
        'article_id'
    ];

    protected $table = 'preassembled_articles';
}

