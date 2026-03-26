<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $challenge = $request->user()->currentChallenge();
        $completions = [];
        $hasCompleted = false;

        if ($challenge) {
            $completions = $challenge->completions()
                ->with('user')
                ->whereDate('date', today())
                ->latest('completed_at')
                ->get();

            $hasCompleted = $challenge->completions()
                ->whereDate('date', today())
                ->whereBelongsTo($request->user())
                ->exists();
        }

        return Inertia::render('Dashboard', [
            'hasChallenge' => (bool) $challenge,
            'challenge' => $challenge,
            'todayTarget' => $challenge?->today_target,
            'todayCompletions' => $completions,
            'hasCompletedToday' => $hasCompleted,
        ]);
    }
}
