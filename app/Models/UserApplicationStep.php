<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserApplicationStep extends Model
{
    protected $fillable = ['application_id', 'application_step_id', 'status'];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function step()
    {
        return $this->belongsTo(ApplicationStep::class, 'application_step_id');
    }
}