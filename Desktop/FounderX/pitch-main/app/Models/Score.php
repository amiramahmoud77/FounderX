<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Score extends Model
{
    /** @use HasFactory<\Database\Factories\ScoreFactory> */
    use HasFactory;
    protected $fillable = [
        'market_score',
        'competition_score',
        'problem_score',
        'business_model_score',
        'solution_score',
        'GTM_strategy_score',
        'traction_score',
        'team_score',
        'product_tech_stack_score',
        'traction_results_score',
        'financials_investment_score',
        'overall_score',
        'pitch_id',
        'pitch_text_id',
    ];
    public function pitch():BelongsTo{
        return $this->belongsTo(Pitch::class);
    }
    public function pitchText():BelongsTo{
        return $this->belongsTo(PitchText::class,'pitch_text_id');
    }
    public function calcOverAllScore():void{
        $scores=[
            $this->market_score,
            $this->competition_score,
            $this->problem_score,
            $this->business_model_score,
            $this->solution_score,
            $this->GTM_strategy_score,
            $this->traction_score,
            $this->team_score,
            $this->product_tech_stack_score,
            $this->traction_results_score,
            $this->financials_investment_score,
        ];
        $this->overall_score=array_sum($scores)/count($scores);
        $this->save();
    }
    public function recommendations():BelongsToMany{
        return $this->belongsToMany(Recommendation::class);
    }
}
