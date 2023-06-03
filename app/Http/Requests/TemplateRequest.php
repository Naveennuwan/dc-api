<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'template_name' => 'required|string|max:50',
            'template_type_id' => 'required|exists:template_types,id',
            'is_active' => 'boolean',
            'template_bodies' => 'required|array',
            'template_bodies.*.product_id' => 'required|exists:products,id',
            'template_bodies.*.quantity' => 'required|numeric|min:0',
        ];
    }
}
