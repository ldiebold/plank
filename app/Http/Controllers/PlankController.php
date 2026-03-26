<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlankRequest;
use App\Models\PlankCompletion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PlankController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        $user = $request->user();

        $challenge = $user->currentChallenge();

        if (! $challenge) {
            return redirect()->route('challenges.index');
        }

        $hasCompletedToday = $challenge->completions()
            ->whereDate('date', today())
            ->whereBelongsTo($user)
            ->exists();

        return Inertia::render('planks/Index', [
            'challenge' => $challenge,
            'todayTarget' => $challenge->today_target,
            'countdownSeconds' => $user->countdown_seconds,
            'hasCompletedToday' => $hasCompletedToday,
        ]);
    }

    public function store(StorePlankRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $plankCompletion = new PlankCompletion;
        $plankCompletion->challenge_id = $validated['challenge_id'];
        $plankCompletion->user_id = $user->id;
        $plankCompletion->date = today();
        $plankCompletion->duration_seconds = $validated['duration_seconds'];
        $plankCompletion->completed_at = now();
        $plankCompletion->save();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Plank completed successfully.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        $challenge = $user->currentChallenge();

        if (! $challenge) {
            return redirect()->route('dashboard');
        }

        // Delete today's completion for this user
        PlankCompletion::query()
            ->where('challenge_id', $challenge->id)
            ->where('user_id', $user->id)
            ->whereDate('date', today())
            ->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Today\'s plank submission deleted.');
    }
}
