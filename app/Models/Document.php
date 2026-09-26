<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'application_id', 'document_type', 'original_filename', 'stored_filename', 'file_path', 'file_size', 'review_status', 'review_comment'];

    protected $casts = [
        'review_status' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
