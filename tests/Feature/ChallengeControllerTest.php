<?php

use App\Http\Controllers\ChallengeController;
use App\Http\Requests\StoreChallengeRequest;
use App\Models\Challenge;
use App\Models\PlankCompletion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('renders the challenges index page when the user is not in a challenge', function () {
    $controller = new ChallengeController;
    $user = new class extends User
    {
        public function isInChallenge(): bool
        {
            return false;
        }

        public function currentChallenge(): ?Challenge
        {
            return null;
        }
    };

    $request = Request::create('/challenges', 'GET');
    $request->setUserResolver(fn () => $user);

    $response = $controller->index($request)->toResponse($request->duplicate(
        server: ['HTTP_X-Inertia' => 'true']
    ));

    $page = json_decode((string) $response->getContent(), true, flags: JSON_THROW_ON_ERROR);

    expect($page['component'])->toBe('challenges/Index');
    expect($page['props']['hasChallenge'])->toBeFalse();
});

it('creates a challenge and joins the creator on store', function () {
    $controller = new ChallengeController;

    $user = User::factory()->create();

    $request = Mockery::mock(StoreChallengeRequest::class);
    $request->shouldReceive('validated')->once()->andReturn([
        'name' => 'Starter Challenge',
        'description' => 'Core strength challenge',
        'starting_time_seconds' => 60,
        'daily_increment_seconds' => 10,
        'goal_time_seconds' => 120,
        'start_date' => today()->toDateString(),
        'is_active' => true,
    ]);
    $request->shouldReceive('user')->andReturn($user);

    $response = $controller->store($request);

    $challenge = Challenge::query()->first();

    expect($challenge)->not->toBeNull();
    expect($challenge->created_by)->toBe($user->id);
    expect($challenge->users()->whereKey($user->id)->exists())->toBeTrue();
    expect($response->getTargetUrl())->toBe(route('challenges.show', $challenge));
});

it('creates a challenge without a goal time', function () {
    $controller = new ChallengeController;

    $user = User::factory()->create();

    $request = Mockery::mock(StoreChallengeRequest::class);
    $request->shouldReceive('validated')->once()->andReturn([
        'name' => 'Starter Challenge',
        'description' => 'Core strength challenge',
        'starting_time_seconds' => 60,
        'daily_increment_seconds' => 10,
        'goal_time_seconds' => null,
        'start_date' => today()->toDateString(),
        'is_active' => true,
    ]);
    $request->shouldReceive('user')->andReturn($user);

    $response = $controller->store($request);

    $challenge = Challenge::query()->first();

    expect($challenge)->not->toBeNull();
    expect($challenge->created_by)->toBe($user->id);
    expect($challenge->goal_time_seconds)->toBeNull();
    expect($challenge->users()->whereKey($user->id)->exists())->toBeTrue();
    expect($response->getTargetUrl())->toBe(route('challenges.show', $challenge));
});

it('renders the challenge show page with today completions', function () {
    $controller = new ChallengeController;

    $creator = User::factory()->create();
    $member = User::factory()->create();

    $challenge = Challenge::query()->create([
        'name' => 'Daily Plank',
        'description' => 'Build consistency',
        'created_by' => $creator->id,
        'starting_time_seconds' => 60,
        'daily_increment_seconds' => 5,
        'goal_time_seconds' => 120,
        'start_date' => today()->subDay(),
        'is_active' => true,
    ]);

    $challenge->users()->attach($creator->id);
    $challenge->users()->attach($member->id);

    PlankCompletion::query()->create([
        'challenge_id' => $challenge->id,
        'user_id' => $member->id,
        'date' => today(),
        'duration_seconds' => 65,
        'completed_at' => now(),
    ]);

    $request = Request::create('/challenges/'.$challenge->id, 'GET');
    $response = $controller->show($challenge)->toResponse($request->duplicate(
        server: ['HTTP_X-Inertia' => 'true']
    ));
    $page = json_decode((string) $response->getContent(), true, flags: JSON_THROW_ON_ERROR);

    expect($page['component'])->toBe('challenges/Show');
    expect($page['props']['challenge']['id'])->toBe($challenge->id);
    expect($page['props']['members'])->toHaveCount(2);
    expect($page['props']['todayCompletions'])->toHaveCount(1);
});

it('joins a challenge by invite code', function () {
    $controller = new ChallengeController;

    $creator = User::factory()->create();
    $joiner = new class extends User
    {
        public function isInChallenge(): bool
        {
            return false;
        }

        public function currentChallenge(): ?Challenge
        {
            return null;
        }
    };

    $persistedJoiner = User::factory()->create();
    $joiner->forceFill($persistedJoiner->getAttributes());
    $joiner->exists = true;

    $challenge = Challenge::query()->create([
        'name' => 'Invite Challenge',
        'description' => null,
        'created_by' => $creator->id,
        'starting_time_seconds' => 45,
        'daily_increment_seconds' => 5,
        'goal_time_seconds' => 90,
        'start_date' => today(),
        'is_active' => true,
    ]);

    $request = Request::create('/join/'.$challenge->invite_code, 'GET');
    $request->setUserResolver(fn () => $joiner);

    $response = $controller->join($request, $challenge->invite_code);

    expect($challenge->users()->whereKey($persistedJoiner->id)->exists())->toBeTrue();
    expect($response->getTargetUrl())->toBe(route('challenges.show', $challenge));
});

it('allows a user to leave a challenge they are in', function () {
    $controller = new ChallengeController;

    $user = User::factory()->create();

    $challenge = Challenge::query()->create([
        'name' => 'Test Challenge',
        'description' => null,
        'created_by' => $user->id,
        'starting_time_seconds' => 60,
        'daily_increment_seconds' => 10,
        'goal_time_seconds' => 120,
        'start_date' => today(),
        'is_active' => true,
    ]);

    $challenge->users()->attach($user->id);

    $request = Request::create('/challenges/'.$challenge->id.'/leave', 'DELETE');
    $request->setUserResolver(fn () => $user);

    $response = $controller->leave($request, $challenge);

    expect($challenge->users()->whereKey($user->id)->exists())->toBeFalse();
    expect($response->getTargetUrl())->toBe(route('challenges.index'));
});

it('does not allow a user to leave a challenge they are not in', function () {
    $controller = new ChallengeController;

    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $challenge = Challenge::query()->create([
        'name' => 'Test Challenge',
        'description' => null,
        'created_by' => $otherUser->id,
        'starting_time_seconds' => 60,
        'daily_increment_seconds' => 10,
        'goal_time_seconds' => 120,
        'start_date' => today(),
        'is_active' => true,
    ]);

    $challenge->users()->attach($otherUser->id);

    $request = Request::create('/challenges/'.$challenge->id.'/leave', 'DELETE');
    $request->setUserResolver(fn () => $user);

    expect(fn () => $controller->leave($request, $challenge))
        ->toThrow(ValidationException::class, 'You are not a member of this challenge.');
});
