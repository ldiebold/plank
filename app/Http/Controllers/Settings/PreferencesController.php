<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateCountdownRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PreferencesController extends Controller
{
    public function show(Request $request): Response
    {
        return Inertia::render('settings/Preferences', [
            'countdownSeconds' => $request->user()->countdown_seconds,
        ]);
    }

    public function update(UpdateCountdownRequest $request): RedirectResponse
    {
        $request->user()->countdown_seconds = $request->countdown_seconds;
        $request->user()->save();

        return back()->with('status', 'Preferences updated.');
    }
}
