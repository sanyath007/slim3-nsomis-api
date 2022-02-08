<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promote extends Model
{
    protected $connection = "person";
    protected $table = "promotes";
    
    public function person()
    {
        return $this->hasMany(Person::class, 'person_id', 'person_id');
    }

    public function typeposition()
    {
        return $this->belongsTo(TypePosition::class, 'typeposition_id', 'typeposition_id');
    }

    public function typeacademic()
    {
        return $this->belongsTo(TypeAcademic::class, 'typeac_id', 'typeac_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'position_id');
    }

    public function academic()
    {
        return $this->belongsTo(Academic::class, 'academic_id', 'ac_id');
    }
}