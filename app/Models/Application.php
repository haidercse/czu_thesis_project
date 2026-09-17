<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = ['user_id', 'program_id', 'status'];

    public function user() { return $this->belongsTo(User::class); }
    public function program() { return $this->belongsTo(Program::class); }
    public function steps() { return $this->hasMany(UserApplicationStep::class); }

    public function getProgressPercentageAttribute()
    {
        $total = $this->steps->count();
        if ($total === 0) return 0;
        $completed = $this->steps->where('status', 'completed')->count();
        return round(($completed / $total) * 100);
    }
}
