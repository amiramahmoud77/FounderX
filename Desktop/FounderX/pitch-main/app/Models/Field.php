<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{
    /** @use HasFactory<\Database\Factories\FieldFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
    ];
    public function pitches():HasMany{
        return $this->hasMany(Pitch::class);
    }
    public function pitchTexts():HasMany{
        return $this->hasMany(PitchText::class);
    }
}
