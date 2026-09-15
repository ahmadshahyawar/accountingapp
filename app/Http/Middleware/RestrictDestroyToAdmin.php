<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applied to the whole app: any signed-in user can use every module, but
 * deleting a record — reversing a posted, saved transaction — is an admin
 * action only. Works off the ".destroy" route-name convention every
 * Route::resource() (and the two hand-named unit/warehouse delete routes)
 * already follows, so it covers every module without touching each
 * controller or route registration individually.
 */
class RestrictDestroyToAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();

        if ($routeName && str_ends_with($routeName, '.destroy') && ! $request->user()?->isAdmin()) {
            abort(403, 'حذف رکورد فقط برای مدیر سیستم مجاز است.');
        }

        return $next($request);
    }
}
