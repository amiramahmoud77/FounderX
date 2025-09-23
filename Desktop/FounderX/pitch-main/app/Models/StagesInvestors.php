<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StagesInvestors extends Model
{
    /** @use HasFactory<\Database\Factories\StagesInvestorsFactory> */
    use HasFactory;
    public function stage():BelongsTo{
        return $this->belongsTo(Stage::class);
    }
    public function investor():BelongsTo{
        return $this->belongsTo(Investors::class);
    }
}
