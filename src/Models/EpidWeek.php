<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EpidWeek extends Model
{
    protected $connection = "pharma";
    protected $table = "epid_weeks";
}