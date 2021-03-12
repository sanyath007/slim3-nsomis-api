<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArrearPaid extends Model
{
    protected $connection = "arrear";
    protected $table = "arrear_paid";
}