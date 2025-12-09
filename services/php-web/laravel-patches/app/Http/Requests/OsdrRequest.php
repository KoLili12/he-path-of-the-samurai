<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OsdrRequest extends FormRequest
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
            'search' => 'sometimes|string|max:200',
            'sort_by' => 'sometimes|string|in:title,date,relevance',
            'sort_order' => 'sometimes|string|in:asc,desc',
            'page' => 'sometimes|integer|min:1|max:100',
            'per_page' => 'sometimes|integer|min:1|max:50',
            'filter_type' => 'sometimes|string|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'search.max' => 'Search query cannot exceed 200 characters',
            'sort_by.in' => 'Sort by must be one of: title, date, relevance',
            'sort_order.in' => 'Sort order must be: asc or desc',
            'page.min' => 'Page number must be at least 1',
            'page.max' => 'Page number cannot exceed 100',
            'per_page.min' => 'Items per page must be at least 1',
            'per_page.max' => 'Items per page cannot exceed 50',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'ok' => false,
            'error' => [
                'code' => 'VALIDATION_FAILED',
                'message' => 'The given data was invalid.',
                'details' => $validator->errors(),
            ],
        ], 422));
    }
}
