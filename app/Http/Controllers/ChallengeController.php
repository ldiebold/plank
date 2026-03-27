<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChallengeRequest;
use App\Models\Challenge;
use App\Rules\UserNotInChallenge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ChallengeController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        $user = $request->user();

        $challenge = $user->currentChallenge();

        if ($challenge) {
            return to_route('challenges.show', $challenge);
        }

        return Inertia::render('challenges/Index', [
            'hasChallenge' => false,
        ]);
    }

    public function create(Request $request): Response|RedirectResponse
    {
        $user = $request->user();

        if ($user->isInChallenge()) {
            return back()->withErrors([
                'challenge' => 'You are already in a challenge. Leave your current challenge first.',
            ]);
        }

        return Inertia::render('challenges/Create');
    }

    public function store(StoreChallengeRequest $request): RedirectResponse
    {
        $challenge = Challenge::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        $challenge->users()->attach($request->user()->id);

        return to_route('challenges.show', $challenge);
    }

    public function show(Challenge $challenge): Response
    {
        $challenge->load('users');

        $completions = $challenge->completions()
            ->with('user')
            ->whereDate('date', today())
            ->latest('completed_at')
            ->get();

        return Inertia::render('challenges/Show', [
            'challenge' => $challenge,
            'todayTarget' => $challenge->today_target,
            'members' => $challenge->users,
            'todayCompletions' => $completions,
        ]);
    }

    public function join(Request $request, string $inviteCode): RedirectResponse
    {
        $user = $request->user();
        $challenge = Challenge::query()->where('invite_code', $inviteCode)->firstOrFail();

        (new UserNotInChallenge($user))('challenge', $challenge->id, function (string $message): void {
            throw ValidationException::withMessages([
                'challenge' => $message,
            ]);
        });

        $challenge->users()->attach($user->id);

        return to_route('challenges.show', $challenge);
    }

    public function leave(Request $request, Challenge $challenge): RedirectResponse
    {
        $user = $request->user();

        if (! $challenge->users()->whereKey($user->id)->exists()) {
            throw ValidationException::withMessages([
                'challenge' => 'You are not a member of this challenge.',
            ]);
        }

        $challenge->users()->detach($user->id);

        return to_route('challenges.index');
    }
}
