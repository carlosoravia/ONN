<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesArticle extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'descrizione', 'cat_omogenea', 'desc_cat', 'reparto', 'natura'
    ];

    public function lines()
    {
        return $this->hasMany(OrderLine::class);
    }
}
