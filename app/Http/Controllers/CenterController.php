<?php

namespace App\Http\Controllers;
use App\Models\Center;

class CenterController extends Controller
{
    public function GetAll()
    {
        return Center::all();
    }
}
