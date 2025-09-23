<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'pitch_id',
        'pdf_path',
    ];

    public function pitch()
    {
        return $this->belongsTo(Pitch::class);
    }
}

