<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Viewer hanya boleh membaca.
 *
 * Semua request tulis (POST/PUT/PATCH/DELETE) dari akun Viewer ditolak
 * dengan 403, kecuali ganti kata sandi sendiri (route update_password).
 * Sebelum middleware ini, pembatasan Viewer hanya disembunyikan di
 * tampilan, sedangkan server tetap melayani semua aksi tulis.
 */
class ViewerReadOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->level === 'Viewer'
            && ! in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)
            && ! $request->routeIs('update_password')) {
            abort(403, 'Akun Viewer hanya boleh membaca data.');
        }

        return $next($request);
    }
}
