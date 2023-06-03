<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateTypes extends Model
{
    protected $table = "template_types";

    protected $fillable = [
        'template_type',
    ];
}
