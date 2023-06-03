<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stocks';

    protected $fillable = [
        'product_id',
        'expire_date',
        'quantity',
        'price',
        'selling_price',
        'created_by', 
        'center_id',
    ];

    public function stockCenter()
    {
        return $this->belongsTo(Center::class, 'center_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
