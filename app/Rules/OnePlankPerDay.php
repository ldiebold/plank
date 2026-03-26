<?php

namespace App\Rules;

use App\Models\PlankCompletion;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OnePlankPerDay implements ValidationRule
{
    public function __construct(private int|string $challengeId, private int|string $userId) {}

    public function validate(string $_attribute, mixed $_value, Closure $fail): void
    {
        if (PlankCompletion::query()
            ->where('challenge_id', $this->challengeId)
            ->where('user_id', $this->userId)
            ->whereDate('date', today())
            ->exists()) {
            $fail('You have already completed today\'s plank.');
        }
    }
}
