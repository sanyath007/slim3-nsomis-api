<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nurse extends Model
{
    protected $connection = "pharma";
    protected $table = "nurse_person";

    public function person()
    {
        return $this->belongsTo(Person::class, 'cid', 'person_id');
    }
    
    public function academic()
    {
        return $this->belongsTo(Academic::class, 'ac_id', 'ac_id');
    }

    public function hosp()
    {
        return $this->setConnection('default')->belongsTo(Hospcode::class, 'hospcode', 'hospcode');
    }
    
    public function hosppay18()
    {
        return $this->setConnection('default')->belongsTo(Hospcode::class, 'hosppay18', 'hospcode');
    }
}