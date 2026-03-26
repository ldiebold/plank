<?php

use App\Models\Challenge;
use App\Models\PlankCompletion;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    if (! Route::has('challenges.index')) {
        Route::get('/challenges', fn () => 'Challenges')->name('challenges.index');
    }
});

test('guests are redirected to login from planks index', function () {
    get(route('planks.index'))
        ->assertRedirect(route('login'));
});

test('planks index is displayed with completion state', function () {
    $user = User::factory()->create([
        'countdown_seconds' => 45,
    ]);

    $challenge = Challenge::factory()->create([
        'created_by' => $user->id,
        'start_date' => today()->subDay(),
        'starting_time_seconds' => 60,
        'daily_increment_seconds' => 15,
        'goal_time_seconds' => 300,
    ]);

    $challenge->users()->attach($user->id);

    PlankCompletion::factory()->create([
        'challenge_id' => $challenge->id,
        'user_id' => $user->id,
        'date' => today(),
    ]);

    actingAs($user)
        ->get(route('planks.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('planks/Index')
            ->where('challenge.id', $challenge->id)
            ->where('todayTarget', $challenge->today_target)
            ->where('countdownSeconds', 45)
            ->where('hasCompletedToday', true),
        );
});

test('planks index redirects to challenges index when user has no challenge', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('planks.index'))
        ->assertRedirect(route('challenges.index'));
});

test('store creates a plank completion and redirects to dashboard', function () {
    $user = User::factory()->create();

    $challenge = Challenge::factory()->create([
        'created_by' => $user->id,
    ]);

    $challenge->users()->attach($user->id);

    actingAs($user)
        ->post(route('planks.store'), [
            'challenge_id' => $challenge->id,
            'duration_seconds' => 120,
        ])
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('success', 'Plank completed successfully.');

    $completion = PlankCompletion::query()
        ->where('challenge_id', $challenge->id)
        ->where('user_id', $user->id)
        ->whereDate('date', today())
        ->first();

    expect($completion)
        ->not->toBeNull()
        ->duration_seconds->toBe(120)
        ->completed_at->not->toBeNull();
});
