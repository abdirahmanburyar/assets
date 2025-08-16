<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetDocumentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file_path' => [
                'required',
                'string',
                'max:1000', // Reasonable path length
            ],
            'file_name' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'file_path.required' => 'Please select a file to upload.',
            'file_path.string' => 'The file path must be a valid string.',
            'file_path.max' => 'The file path is too long.',
            'file_name.required' => 'Please provide a file name.',
            'file_name.max' => 'The file name must not exceed 255 characters.',
        ];
    }
}
