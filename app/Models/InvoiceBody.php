<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceBody extends Model
{
    use HasFactory;

    protected $table = 'invoice_bodies';

    protected $fillable = [
        'header_id',
        'template_header_id',
        'template_body_id',
        'product_id',
        'quantity',
        'price',
        'selling_price',
    ];

    public function header()
    {
        return $this->belongsTo(InvoiceHeader::class, 'header_id');
    }

    public function templateHeader()
    {
        return $this->belongsTo(TemplateHeader::class, 'template_header_id');
    }

    public function templateBody()
    {
        return $this->belongsTo(TemplateBody::class, 'template_body_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
