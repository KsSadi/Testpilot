<?php

namespace App\Modules\Subsription\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckProjectLimit
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user->canCreateProject()) {
            return redirect()->route('subscription.index')
                ->with('error', 'You have reached your project limit. Please upgrade your plan to create more projects.');
        }

        return $next($request);
    }
}
