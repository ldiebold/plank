<?php

namespace App\Rules;

use App\Models\User;
use Closure;

class UserNotInChallenge
{
    public function __construct(private User $user) {}

    public function __invoke(string $_attribute, mixed $_value, Closure $fail): void
    {
        sprintf('%s%s', $_attribute, (string) $_value);

        if ($this->user->isInChallenge()) {
            $fail('You are already in a challenge. Leave your current challenge first.');
        }
    }
}
