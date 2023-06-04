<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceHeader extends Model
{
    use HasFactory;

    protected $table = 'invoice_headers';

    protected $fillable = [
        'invoice',
        'center_id',
        'discount',
        'patient_id',
        'created_by',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class, 'center_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
