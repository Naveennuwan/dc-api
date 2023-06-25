<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientDisease extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $table = 'patient_diseases';

    protected $fillable = [
        'patient_id',
        'disease_id',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function disease()
    {
        return $this->belongsTo(Disease::class, 'disease_id');
    }
}
