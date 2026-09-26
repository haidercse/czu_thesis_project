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

    public function isApplicableToCountry(?string $country): bool
    {
        if (empty($this->applicable_countries)) {
            return true;
        }

        if (blank($country)) {
            return false;
        }

        return in_array($country, $this->applicable_countries, true);
    }

    public function isRequiredForStudent(?string $country): bool
    {
        return ! empty($this->related_document_type) || $this->isApplicableToCountry($country);
    }
}
