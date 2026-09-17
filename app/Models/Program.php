<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'university_id', 'program_name', 'field_of_study',
        'tuition_fee_annual', 'application_deadline', 'language_proficiency_requirement'
    ];

    public function university()
    {
        return $this->belongsTo(University::class);
    }
}
