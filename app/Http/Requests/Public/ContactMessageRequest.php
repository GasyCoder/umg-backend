<?php

namespace App\Http\Requests\Public;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContactMessageRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // Always return JSON for API calls, even if the client didn't send Accept: application/json.
        throw new HttpResponseException(response()->json([
            'message' => 'Validation error.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
