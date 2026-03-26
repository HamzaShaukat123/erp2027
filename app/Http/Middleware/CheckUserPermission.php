<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserPermission
{
    public function handle($request, Closure $next, $requestType, ...$permission)
    {
        // Session check — no DB query (fixes "too many connections")
        if (!session()->has('user_id')) {
            return redirect('/login');
        }

        $parsedUrl = parse_url($request->url());
        $segments = explode('/', trim($parsedUrl['path'], '/'));
        $requestedSlug = !empty($segments) ? $segments[0] : null;

        $userAccess = session('user_access');
        $moduleAccess = collect($userAccess)->firstWhere('slug', $requestedSlug);

        if ($moduleAccess) {
            if (isset($moduleAccess[$requestType]) && $moduleAccess[$requestType] == 1) {
                return $next($request);
            }
        }

        return redirect('/unauthorized');
    }
}