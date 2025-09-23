<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Team extends Model
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'background',
        'role',
        'pitch_id',
    ];
    public function pitch():BelongsTo{
        return $this->belongsTo(Pitch::class);
    }
    public function user():BelongsTo{
        return $this->belongsTo(User::class);
    }
}
