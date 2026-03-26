<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreChallengeRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'starting_time_seconds' => ['required', 'integer', 'min:1'],
            'daily_increment_seconds' => ['required', 'integer', 'min:1'],
            'goal_time_seconds' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'is_active' => ['boolean'],
        ];
    }
}
