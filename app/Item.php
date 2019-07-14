<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['distance', 'water_status', 'serial', 'distance_max', 'distance_min', 'pump_status', 'auto_status', 'tank_status'];
}
