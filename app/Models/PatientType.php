<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientType extends Model
{
    protected $table = "patient_types";

    protected $fillable = [
        'patient_type',
    ];
}
