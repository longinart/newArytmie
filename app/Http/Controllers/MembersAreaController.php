<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MembersAreaController extends Controller
{
    public function show(Request $request): View
    {
        abort_unless(config('members.enabled'), 404);

        $expected = config('members.password');
        $configured = is_string($expected) && $expected !== '';

        if ($request->session()->get('members_area_unlocked') === true) {
            if (! $configured) {
                $request->session()->forget('members_area_unlocked');
            } else {
                return view('members.dashboard');
            }
        }

        if (! $configured) {
            return view('members.unconfigured');
        }

        return view('members.gate');
    }

    public function unlock(Request $request): RedirectResponse
    {
        abort_unless(config('members.enabled'), 404);

        $expected = config('members.password');
        abort_unless(is_string($expected) && $expected !== '', 503);

        $validated = $request->validate([
            'password' => ['required', 'string', 'max:500'],
        ]);

        if (! hash_equals($expected, $validated['password'])) {
            return back()->withErrors(['password' => 'Heslo není správné.']);
        }

        $request->session()->put('members_area_unlocked', true);

        return redirect()->route('members.index');
    }

    public function lock(Request $request): RedirectResponse
    {
        $request->session()->forget('members_area_unlocked');

        return redirect()->route('members.index');
    }
}
