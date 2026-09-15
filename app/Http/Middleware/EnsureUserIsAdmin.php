<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Full-route admin gate — for screens only an admin should reach at all
 * (user management). See RestrictDestroyToAdmin for the lighter "any
 * authenticated user can use the module, only admins can delete" rule
 * applied to every other module.
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->isAdmin(), 403, 'این بخش فقط برای مدیر سیستم قابل دسترسی است.');

        return $next($request);
    }
}
