<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDrugList extends Model
{
    protected $connection = "pharma";
    protected $table = "user_drug_lists";
}