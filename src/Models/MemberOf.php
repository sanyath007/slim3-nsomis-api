<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberOf extends Model
{
    protected $connection = "person";
    protected $table = "level";

    public function person()
    {
        return $this->hasMany(Person::class, 'person_id', 'person_id');
    }

    public function duty()
    {
        return $this->belongsTo(Duty::class, 'duty_id', 'duty_id');
    }

    public function depart()
    {
        return $this->belongsTo(Depart::class, 'depart_id', 'depart_id');
    }

    public function faction()
    {
        return $this->belongsTo(Faction::class, 'faction_id', 'faction_id');
    }
}