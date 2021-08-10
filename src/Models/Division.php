<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $connection = "person";
    protected $table = "ward";

    public function depart()
    {
        return $this->belongTo(Depart::class, 'ward_id', 'ward_id');
    }
    
    public function memberOf()
    {
        return $this->hasMany(MemberOf::class, 'ward_id', 'ward_id');
    }
}