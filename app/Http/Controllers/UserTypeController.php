<?php

namespace App\Http\Controllers;

use App\Models\UserType;

class UserTypeController extends Controller
{
    public function GetAll()
    {
        return UserType::all();
    }
}
