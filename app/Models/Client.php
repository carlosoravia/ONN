<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['code', 'ragione_sociale'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
