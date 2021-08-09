<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $connection = "person";
    protected $table = "office_sit";

    public function person()
    {
        return $this->hasMany(Person::class, 'office_id', 'ward_id');
    }
}