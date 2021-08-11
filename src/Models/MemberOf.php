<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberOf extends Model
{
    protected $connection   = "person";
    protected $table        = "level";
    protected $primaryKey   = "level_id";
    // public $incrementing = false; //ไม่ใช้ options auto increment
    public $timestamps = false; //ไม่ใช้ field updated_at และ created_at

    public function person()
    {
        return $this->hasMany(Person::class, 'person_id', 'person_id');
    }

    public function duty()
    {
        return $this->belongsTo(Duty::class, 'duty_id', 'duty_id');
    }

    public function faction()
    {
        return $this->belongsTo(Faction::class, 'faction_id', 'faction_id');
    }

    public function depart()
    {
        return $this->belongsTo(Depart::class, 'depart_id', 'depart_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'ward_id', 'ward_id');
    }
}