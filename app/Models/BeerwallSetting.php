<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeerwallSetting extends Model
{
    protected $fillable = [
        'monday_close_time',
        'tuesday_close_time',
        'wednesday_close_time',
        'thursday_close_time',
        'friday_close_time',
        'saturday_close_time',
        'sunday_close_time',
    ];
}
