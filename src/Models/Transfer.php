<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $connection = "person";
    protected $table = "transfers";
    
    public function person()
    {
        return $this->hasMany(Person::class, 'person_id', 'transfer_person');
    }
}