<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'university_id', 'program_name', 'field_of_study',
        'tuition_fee_annual', 'application_deadline', 'language_proficiency_requirement', 'minimum_gpa', 'official_source_url'
    ];

    protected static function booted()
    {
        static::saving(function (self $program) {
            $program->program_name = trim($program->program_name);
            $program->field_of_study = trim($program->field_of_study);
            $program->language_proficiency_requirement = $program->language_proficiency_requirement ? trim($program->language_proficiency_requirement) : null;

            if (!empty($program->official_source_url)) {
                $program->official_source_url = rtrim(trim($program->official_source_url), '/');
            }
        });
    }

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
