<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckRouteAccess
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        $routeName = $request->route()->getName();
    
        // Get the numeric ID of the current route
        $routeId = DB::table('routes')
            ->where('name', $routeName)
            ->value('id');
    
        if (!$routeId) {
            abort(403, 'Route not registered.');
        }
    
        // Ensure that this exact role_id + route_id combination exists
        $hasAccess = DB::table('role_route')
        ->where('route_id', $routeId)
        ->where('role_id', auth()->user()->role_id)
        ->first(); // returns a single object or null
    

        if (!$hasAccess) {
            abort(403, 'You are not authorized to access this page.');
        }
        
            



    
        return $next($request);
    }
    
}
