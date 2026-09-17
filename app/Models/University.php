<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    protected $fillable = ['name', 'location', 'website_url'];

    public function programs()
    {
        return $this->hasMany(Program::class);
    }
}
