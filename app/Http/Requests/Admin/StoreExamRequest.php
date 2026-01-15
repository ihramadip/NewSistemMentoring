<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // For now, we'll assume any authenticated admin can create an exam.
        // You might add more specific authorization logic here if needed.
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level_id' => 'nullable|exists:levels,id',
            'duration_minutes' => 'nullable|integer|min:1',
            'published_at' => 'nullable|date',
        ];
    }
}
