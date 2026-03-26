<?php

use App\Models\User;
use App\Rules\OnePlankPerDay;
use App\Rules\UserNotInChallenge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('fails when the user is already in a challenge', function () {
    $rule = new UserNotInChallenge(new class extends User
    {
        public function isInChallenge(): bool
        {
            return true;
        }
    });

    $message = null;

    $rule('challenge', null, function (string $error) use (&$message): void {
        $message = $error;
    });

    expect($message)->toBe('You are already in a challenge. Leave your current challenge first.');
});

it('passes when the user is not in a challenge', function () {
    $rule = new UserNotInChallenge(new class extends User
    {
        public function isInChallenge(): bool
        {
            return false;
        }
    });

    $message = null;

    $rule('challenge', null, function (string $error) use (&$message): void {
        $message = $error;
    });

    expect($message)->toBeNull();
});

it('fails when the user already completed a plank today for the challenge', function () {
    $user = User::factory()->create();
    $challengeId = DB::table('challenges')->insertGetId([
        'name' => 'Daily Core Builder Challenge',
        'description' => 'A test challenge',
        'created_by' => $user->id,
        'starting_time_seconds' => 60,
        'daily_increment_seconds' => 5,
        'goal_time_seconds' => 300,
        'invite_code' => Str::random(12),
        'start_date' => today()->toDateString(),
        'is_active' => true,
    ]);

    DB::table('plank_completions')->insert([
        'challenge_id' => $challengeId,
        'user_id' => $user->id,
        'date' => today()->toDateString(),
        'duration_seconds' => 60,
        'completed_at' => now(),
    ]);

    $rule = new OnePlankPerDay($challengeId, $user->id);
    $message = null;

    $rule('plank', null, function (string $error) use (&$message): void {
        $message = $error;
    });

    expect($message)->toBe('You have already completed today\'s plank.');
});

it('passes when there is no plank completion today for the challenge', function () {
    $user = User::factory()->create();
    $challengeId = DB::table('challenges')->insertGetId([
        'name' => 'Daily Core Builder Challenge',
        'description' => 'A test challenge',
        'created_by' => $user->id,
        'starting_time_seconds' => 60,
        'daily_increment_seconds' => 5,
        'goal_time_seconds' => 300,
        'invite_code' => Str::random(12),
        'start_date' => today()->toDateString(),
        'is_active' => true,
    ]);

    DB::table('plank_completions')->insert([
        'challenge_id' => $challengeId,
        'user_id' => $user->id,
        'date' => now()->subDay()->toDateString(),
        'duration_seconds' => 60,
        'completed_at' => now()->subDay(),
    ]);

    $rule = new OnePlankPerDay($challengeId, $user->id);
    $message = null;

    $rule('plank', null, function (string $error) use (&$message): void {
        $message = $error;
    });

    expect($message)->toBeNull();
});
