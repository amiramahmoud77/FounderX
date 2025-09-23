<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pitch extends Model
{
    /** @use HasFactory<\Database\Factories\PitchFactory> */
    use HasFactory;
    protected $fillable = [
        'title',
        'problem',
        'solution',
        'market',
        'product_tech_stack',
        'business_model',
        'competition',
        'market_strategy',
        'traction_results',
        'team_info',
        'financials_investment',
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
    public function feedbacks(){
        return $this->hasMany(Feedback::class);
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
    public function teams():HasMany{
        return $this->hasMany(Team::class);
    }

}
