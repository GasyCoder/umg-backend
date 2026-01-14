<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePartnerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['SuperAdmin', 'Validateur']) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|in:national,international',
            'website_url' => 'nullable|url|max:255',
            'country' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_featured' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'logo' => 'nullable|image|max:2048',
            'logo_id' => 'nullable|integer|exists:media,id',
        ];
    }
}
