<?php

namespace App\Http\Controllers;

use App\Models\PatientType;

class PatientTypeController extends Controller
{
    public function GetAll()
    {
        return PatientType::all();
    }
}