<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchedulingDetail extends Model
{
    protected $connection = "person";
    protected $table = "scheduling_detail";
}