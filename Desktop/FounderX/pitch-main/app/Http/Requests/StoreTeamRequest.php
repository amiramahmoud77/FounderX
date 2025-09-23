<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check()&&(Auth::user()->isAdmin()||Auth::user()->isFounder());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'=>'required|string',
            'background'=>"required|string",
            'role'=>"required|in:COO,CEO,CTO",
            'pitch_id'=>"required|exists:pitches,id",
        ];
    }
    public function messages():array{
        return [
            'pitch_id.exists'=>"this pitches does not exist",
            '*.required'=>"the value must be choosen",
            '*.string'=>"the value must string",
            'role.in'=>"the value must COO or CTO or CEO",
        ];
    }
}
