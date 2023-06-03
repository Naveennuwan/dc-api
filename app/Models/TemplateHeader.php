<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateHeader extends Model
{
    use HasFactory;
    protected $table = 'template_headers';

    protected $fillable = [
        'template_name',
        'template_type_id',
        'template_center_id',
        'is_active',
        'created_by',
        'updated_by',
        'is_deleted',
        'deleted_by',
        'deleted_at',
    ];

    public function templateType()
    {
        return $this->belongsTo(TemplateTypes::class, 'template_type_id');
    }

    public function templateCenter()
    {
        return $this->belongsTo(Center::class, 'template_center_id');
    }

    public function templateBodies()
    {
        return $this->hasMany(TemplateBody::class, 'template_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
