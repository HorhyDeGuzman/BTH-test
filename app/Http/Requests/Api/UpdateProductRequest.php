<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Supports full (PUT) and partial (PATCH) updates.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isPatch = $this->isMethod('PATCH');
        $required = $isPatch ? 'sometimes' : 'required';

        return [
            'name' => [$required, 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'price' => [$required, 'numeric', 'gt:0'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'category_id' => [$required, 'integer', 'exists:categories,id'],
        ];
    }
}
