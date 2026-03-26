<?php

use App\Http\Requests\StoreChallengeRequest;
use App\Http\Requests\StorePlankRequest;
use App\Http\Requests\UpdateCountdownRequest;
use App\Models\Challenge;
use App\Models\User;
use App\Rules\OnePlankPerDay;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('defines the challenge store rules', function (): void {
    $request = StoreChallengeRequest::create('/', 'POST');

    expect($request->authorize())->toBeTrue();
    expect($request->rules())->toBe([
        'name' => ['required', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'starting_time_seconds' => ['required', 'integer', 'min:1'],
        'daily_increment_seconds' => ['required', 'integer', 'min:1'],
        'goal_time_seconds' => ['nullable', 'integer', 'min:1'],
        'start_date' => ['required', 'date', 'after_or_equal:today'],
        'is_active' => ['boolean'],
    ]);
});

it('authorizes plank creation only for challenge members', function (): void {
    $user = User::factory()->create();
    $challenge = Challenge::factory()->create([
        'created_by' => $user->id,
    ]);

    $request = StorePlankRequest::create('/', 'POST', [
        'challenge_id' => $challenge->id,
    ]);

    $request->setUserResolver(fn () => $user);

    expect($request->authorize())->toBeFalse();

    $user->challenges()->attach($challenge->id, [
        'joined_at' => now(),
    ]);

    expect($request->authorize())->toBeTrue();
    expect(collect($request->rules()['duration_seconds'])->contains(fn ($rule): bool => $rule instanceof OnePlankPerDay))->toBeTrue();
});

it('defines the countdown update rules', function (): void {
    $request = UpdateCountdownRequest::create('/', 'PATCH');

    expect($request->authorize())->toBeTrue();
    expect($request->rules())->toBe([
        'countdown_seconds' => ['required', 'integer', 'between:3,30'],
    ]);
});
