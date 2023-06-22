<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientAlergy extends Model
{
    use HasFactory;

    protected $table = 'patient_alergies';

    protected $fillable = [
        'patient_id',
        'alergy_id',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function alergy()
    {
        return $this->belongsTo(Alergy::class, 'alergy_id');
    }
}
