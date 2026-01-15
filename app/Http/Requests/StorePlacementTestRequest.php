<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlacementTestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only authenticated users can submit the test.
        // Additional logic could be added here, e.g., check if the user is a mentee.
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
            'audio_recording' => ['required', 'file', 'mimes:mp3,wav,m4a,ogg', 'max:10240'], // 10MB Max
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'string'],
        ];
    }
}