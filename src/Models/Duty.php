<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Duty extends Model
{
    protected $connection = "person";
    protected $table = "duty";
    
    public function memberOf()
    {
        return $this->hasMany(MemberOf::class, 'duty_id', 'duty_id');
    }
}