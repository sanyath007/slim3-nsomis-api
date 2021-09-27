<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $connection = "person";
    protected $table = "personal";
    protected $primaryKey = 'person_id';
    public $incrementing = false; //ไม่ใช้ options auto increment
    public $timestamps = false; //ไม่ใช้ field updated_at และ created_at

    public function nurse()
    {
        return $this->hasOne(Nurse::class);
    }

    public function prefix()
    {
        return $this->belongsTo(Prefix::class, 'person_prefix', 'prefix_id');
    }

    public function typeposition()
    {
        return $this->belongsTo(TypePosition::class, 'typeposition_id', 'typeposition_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'position_id');
    }

    public function academic()
    {
        return $this->belongsTo(Academic::class, 'ac_id', 'ac_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id', 'ward_id');
    }

    public function memberOf()
    {
        return $this->belongsTo(MemberOf::class, 'person_id', 'person_id');
    }
}