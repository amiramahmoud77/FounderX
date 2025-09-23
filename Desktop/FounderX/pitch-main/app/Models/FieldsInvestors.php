<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldsInvestors extends Model
{
    /** @use HasFactory<\Database\Factories\FieldsInvestorsFactory> */
    use HasFactory;
    public function investor():BelongsTo{
        return $this->belongsTo(Investors::class);
    }
    public function field():BelongsTo{
        return $this->belongsTo(Field::class);
    }
}
