<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Batasi route ke level user tertentu.
 *
 * Pakai di route sebagai: ->middleware('level:Administrator')
 * atau beberapa level: ->middleware('level:Administrator,Viewer').
 * Level yang tidak cocok mendapat 403.
 */
class EnsureUserLevel
{
    public function handle(Request $request, Closure $next, string ...$levels): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->level, $levels, true)) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
