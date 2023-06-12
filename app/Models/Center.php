<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Center extends Model
{
    protected $table = "centers";

    protected $fillable = [
        'center',
        'address_01',
        'address_02',
        'contact_no',
        'work_days',
        'service_time',
    ];
}
