<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curp extends Model
{
    protected $table = 'curps';
    protected $fillable = ['curp','datos'];
}
