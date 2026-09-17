<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationStep extends Model
{
    protected $fillable = [
        'step_name',
        'step_description',
        'step_order',
        'applicable_countries',
        'related_document_type',
    ];

    protected $casts = [
        'applicable_countries' => 'array',
    ];
}
