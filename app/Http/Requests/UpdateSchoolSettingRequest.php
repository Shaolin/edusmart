<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolSettingRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'next_term_begins' => ['nullable', 'date'],
            'next_term_school_fees' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'next_term_begins.date' => 'Please enter a valid resumption date.',
            'next_term_school_fees.numeric' => 'School fees must be a valid number.',
            'next_term_school_fees.min' => 'School fees cannot be negative.',
        ];
    }
}