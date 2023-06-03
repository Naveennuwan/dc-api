<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateBody extends Model
{
    use HasFactory;

    protected $table = 'template_bodies';

    protected $fillable = [
        'template_id',
        'product_id',
        'quantity',
    ];
    
    public function templateHeader()
    {
        return $this->belongsTo(TemplateHeader::class, 'template_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
