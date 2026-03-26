<?php

namespace App\Http\Requests;

use App\Models\Challenge;
use App\Rules\OnePlankPerDay;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Schema;

class StorePlankRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        $challengeQuery = Challenge::query()
            ->join('challenge_user', 'challenge_user.challenge_id', '=', 'challenges.id')
            ->where('challenge_user.user_id', $user->id)
            ->where('challenges.id', $this->integer('challenge_id'));

        if (Schema::hasColumn('challenge_user', 'active')) {
            $challengeQuery->where('challenge_user.active', true);
        }

        return $challengeQuery->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'challenge_id' => ['required', 'integer', 'exists:challenges,id'],
            'duration_seconds' => [
                'required',
                'integer',
                'min:1',
                new OnePlankPerDay($this->integer('challenge_id'), $this->user()?->getAuthIdentifier() ?? 0),
            ],
        ];
    }
}
