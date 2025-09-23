<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PitchText extends Model
{
    /** @use HasFactory<\Database\Factories\PitchTextFactory> */
    use HasFactory;
    protected $fillable = [
        'text',
        'status',
        'user_id',
        'field_id',
        'stage_id',
    ];
    public function score():HasOne{
        return $this->hasOne(Score::class);
    }
    public function user():BelongsTo{
        return $this->belongsTo(User::class);
    }
    public function stage():BelongsTo{
        return $this->belongsTo(Stage::class);
    }
    public function field():BelongsTo{
        return $this->belongsTo(Field::class);
    }
    public function scopeIsscored($query){
        return $query->where('status','scored');
    }
    public function scopeIsdraft($query){
        return $query->where('status','draft');
    }
    public function scopeIssubmitted($query){
        return $query->where('status','submitted');
    }
}
