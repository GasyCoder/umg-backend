<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['SuperAdmin','Redacteur','Validateur']) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes','required','string','max:255'],
            'slug' => ['sometimes','string','max:255'],
            'excerpt' => ['nullable','string'],
            'content_html' => ['sometimes','required','string'],
            'content_markdown' => ['nullable','string'],

            'cover_image_id' => ['nullable','exists:media,id'],

            'category_ids' => ['nullable','array'],
            'category_ids.*' => ['integer','exists:categories,id'],

            'tag_ids' => ['nullable','array'],
            'tag_ids.*' => ['integer','exists:tags,id'],

            'gallery' => ['nullable','array'],
            'gallery.*.media_id' => ['required','exists:media,id'],
            'gallery.*.position' => ['nullable','integer','min:0'],
            'gallery.*.caption' => ['nullable','string','max:255'],

            'is_featured' => ['sometimes','boolean'],
            'is_pinned' => ['sometimes','boolean'],

            'seo_title' => ['nullable','string','max:255'],
            'seo_description' => ['nullable','string','max:255'],

            'status' => ['sometimes','string','in:draft,published,archived'],
            // notify_subscribers usually only relevant on update if we re-publish, handled in controller
            'notify_subscribers' => ['sometimes','boolean'],
        ];
    }
}
