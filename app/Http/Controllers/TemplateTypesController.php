<?php

namespace App\Http\Controllers;

use App\Models\TemplateTypes;

class TemplateTypesController extends Controller
{
    public function GetAll()
    {
        return TemplateTypes::all();
    }
}
