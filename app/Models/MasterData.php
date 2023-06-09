<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterData extends Model
{
    use HasFactory;

    protected $table = 'master_data';

    protected $fillable = [
        'profite',
        'discount', 
        'center_id',
    ];

    public function stockCenter()
    {
        return $this->belongsTo(Center::class, 'center_id');
    }
}
