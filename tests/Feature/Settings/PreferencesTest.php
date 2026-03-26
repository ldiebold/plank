<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;

test('preferences page is displayed', function () {
    $user = User::factory()->create([
        'countdown_seconds' => 30,
    ]);

    actingAs($user)
        ->get(route('preferences.edit'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/Preferences')
            ->where('countdownSeconds', 30),
        );
});

test('countdown preference can be updated', function () {
    $user = User::factory()->create([
        'countdown_seconds' => 30,
    ]);

    actingAs($user)
        ->from(route('preferences.edit'))
        ->put(route('preferences.update'), [
            'countdown_seconds' => 60,
        ])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('status', 'Preferences updated.')
        ->assertRedirect(route('preferences.edit'));

    expect($user->refresh()->countdown_seconds)->toBe(60);
});
