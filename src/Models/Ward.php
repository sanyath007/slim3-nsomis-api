<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hospcode extends Model
{
    protected $table = "ward";

    public function productivity()
    {
        return $this->hasMany(Productivity::class, 'ward', 'ward');
    }
}