<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    protected $fillable = ['name', 'location', 'website_url'];

    protected static function booted()
    {
        static::saving(function (self $university) {
            $university->name = trim($university->name);
            $university->location = trim($university->location);

            if (!empty($university->website_url)) {
                $university->website_url = rtrim(trim($university->website_url), '/');
            }
        });
    }

    public function programs()
    {
        return $this->hasMany(Program::class);
    }
}
