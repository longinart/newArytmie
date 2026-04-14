<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMembersAreaUnlocked
{
    /**
     * Podstránky /pro-cleny/* jen po zadání sdíleného hesla (session).
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(config('members.enabled'), 404);

        if ($request->session()->get('members_area_unlocked') !== true) {
            return redirect()->route('members.index');
        }

        $password = config('members.password');
        if (! is_string($password) || $password === '') {
            return redirect()->route('members.index');
        }

        return $next($request);
    }
}
