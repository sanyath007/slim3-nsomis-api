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

    public function newFaction()
    {
        return $this->belongsTo(Faction::class, 'new_faction', 'faction_id');
    }

    public function oldFaction()
    {
        return $this->belongsTo(Faction::class, 'old_faction', 'faction_id');
    }

    public function newDepart()
    {
        return $this->belongsTo(Depart::class, 'new_depart', 'depart_id');
    }

    public function oldDepart()
    {
        return $this->belongsTo(Depart::class, 'old_depart', 'depart_id');
    }
}