<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreScoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check()&&Auth::user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'market_score'=>'required|numeric|min:0|max:100',
            'competition_score'=>"required|numeric|min:0|max:100",
            'problem_score'=>"required|numeric|min:0|max:100",
            'business_model_score'=>"required|numeric|min:0|max:100",
            'solution_score'=>"required|numeric|min:0|max:100",
            'GTM_strategy_score'=>"required|numeric|min:0|max:100",
            'traction_score'=>"required|numeric|min:0|max:100",
            'team_score'=>"required|numeric|min:0|max:100",
            'product_tech_stack_score'=>"required|numeric|min:0|max:100",
            'traction_results_score'=>"required|numeric|min:0|max:100",
            'financials_investment_score'=>"required|numeric|min:0|max:100",
            'pitch_id'=>"required|exists:pitches,id",
        ];
    }
    public function messages():array{
        return [
            'pitch_id.exists'=>"this pitch does not exist",
            '*.required'=>"the value must be choosen",
            '*.numeric'=>"the value must numeric",
            '*.max'=>"the value must not be greater than 100",
            '*.min'=>"the value must not be less than 0",
        ];
    }
}
