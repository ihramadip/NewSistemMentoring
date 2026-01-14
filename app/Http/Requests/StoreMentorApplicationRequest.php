<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreMentorApplicationRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'npm' => ['required', 'string', 'max:255', 'unique:users,npm'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'faculty_id' => ['required', 'exists:faculties,id'],
            'gender' => ['required', 'in:male,female'],
            'cv' => ['required', 'file', 'mimes:pdf', 'max:2048'], // 2MB Max
            'recording' => ['required', 'file', 'mimes:mp3,wav,m4a', 'max:10240'], // 10MB Max
            'btaq_history' => ['required', 'string', 'max:5000'],
        ];
    }
}
