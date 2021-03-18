<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    protected $table = "ward";

    public function productivity()
    {
        return $this->hasMany(Productivity::class, 'ward', 'ward');
    }
}