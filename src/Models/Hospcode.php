<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hospcode extends Model
{
    protected $table = "hospcode";

    public function nurse()
    {
        return $this->hasMany(Nurse::class, 'hosppay18', 'hospcode');
    }
}