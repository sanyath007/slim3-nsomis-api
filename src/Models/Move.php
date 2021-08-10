<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Move extends Model
{
    protected $connection = "person";
    protected $table = "moves";
    
    public function person()
    {
        return $this->hasMany(Person::class, 'person_id', 'move_person');
    }
}