<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Investors extends Model
{
    use HasFactory;

    protected $fillable = [
        'focus_field',
        'company',
        'min_charge',
        'max_charge',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fields():BelongsToMany{
        return $this->belongsToMany(Field::class,'fields_investors');
    }
    public function stages():BelongsToMany{
        return $this->belongsToMany(Stage::class,'stages_investors');
    }
    public function recommendations():BelongsToMany{
        return $this->belongsToMany(Recommendation::class);
    }
}
