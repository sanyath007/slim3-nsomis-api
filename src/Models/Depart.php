<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depart extends Model
{
    protected $connection = "person";
    protected $table = "depart";

    public function nurse()
    {
        return $this->hasOne(Nurse::class);
    }
    
    public function prefix()
    {
        return $this->belongsTo(Prefix::class, 'person_prefix', 'prefix_id');
    }
}