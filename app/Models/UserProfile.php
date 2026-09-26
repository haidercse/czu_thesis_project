<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'country_of_origin',
        'previous_degree',
        'previous_institution',
        'target_field',
        'gpa',
        'language_test_type',
        'language_test_score',
        'annual_budget',
        'preferred_city',
        'target_intake',
    ];

    protected $casts = [
        'gpa' => 'decimal:2',
        'language_test_score' => 'decimal:2',
        'annual_budget' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
