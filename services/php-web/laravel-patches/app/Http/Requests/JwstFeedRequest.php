<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class JwstFeedRequest extends FormRequest
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
            'source' => 'sometimes|string|in:jpg,suffix,program',
            'suffix' => 'sometimes|string|max:50',
            'program' => 'sometimes|string|max:20',
            'instrument' => 'sometimes|string|in:NIRCam,MIRI,NIRISS,NIRSpec,FGS',
            'page' => 'sometimes|integer|min:1|max:1000',
            'perPage' => 'sometimes|integer|min:1|max:60',
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
            'source.in' => 'Source must be one of: jpg, suffix, program',
            'instrument.in' => 'Instrument must be one of: NIRCam, MIRI, NIRISS, NIRSpec, FGS',
            'page.min' => 'Page number must be at least 1',
            'page.max' => 'Page number cannot exceed 1000',
            'perPage.min' => 'Items per page must be at least 1',
            'perPage.max' => 'Items per page cannot exceed 60',
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
