<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePitchTextRequest extends FormRequest
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
            'text'=>'required|string',
            'status'=>"required|in:submitted,draft,scored",
            'field_id'=>"required|exists:fields,id",
            'stage_id'=>"required|exists:stages,id",
        ];
    }
    public function messages():array{
        return [
            '*.required'=>"the value must be choosen",
            '*.string'=>"the value must string",
            'status.in'=>"the value must submitted or draft or scored",
        ];
    }
}
