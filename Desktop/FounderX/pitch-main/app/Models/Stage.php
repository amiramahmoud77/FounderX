<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stage extends Model
{
    /** @use HasFactory<\Database\Factories\StageFactory> */
    use HasFactory;
    public function pitch():HasMany{
        return $this->hasMany(Pitch::class);
    }
    public function pitchText():HasMany{
        return $this->hasMany(PitchText::class);
    }
    public function investors():BelongsToMany{
        return $this->belongsToMany(Investor::class,'stages_investors');
    }
}
