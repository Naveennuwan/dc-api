<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientRequest extends FormRequest
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
            'patient_name' => 'required|string|max:100',
            'patient_incharge' => 'required|string|max:100',
            'patient_address' => 'required|string|max:300',
            'patient_contact_no' => 'required|string|max:300',
            'patient_type_id' => 'required|exists:patient_types,id',
        ];
    }
}
