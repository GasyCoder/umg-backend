<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['SuperAdmin','Redacteur','Validateur']) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'document_category_id' => ['required','exists:document_categories,id'],
            'file_id' => ['required','exists:media,id'],
        ];
    }
}
