<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'score_id',
        'investor_id',
        'match_score',
    ];


    public function score()
    {
        return $this->belongsTo(Score::class);
    }

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
}



