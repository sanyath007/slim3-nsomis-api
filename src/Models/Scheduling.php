<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scheduling extends Model
{
    protected $connection = "scheduling";
    protected $table = "schedulings";

    public function shifts()
    {
        return $this->hasMany(SchedulingDetail::class, 'scheduling_id', 'id');
    }
}