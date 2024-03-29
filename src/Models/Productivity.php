<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productivity extends Model
{
    protected $connection = "pharma";
    protected $table = "productivities";

    public function ward()
    {
        return $this->setConnection('default')->belongsTo(Ward::class, 'ward', 'ward');
    }
}