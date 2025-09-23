<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreStageRequest extends FormRequest
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
     * * Seed - early stage - growing stage
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'stage'=>"required|in:Seed,early_stage,growing_stage",
        ];
    }
}
