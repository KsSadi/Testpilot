<?php

namespace App\Modules\Subsription\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckModuleLimit
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user->canCreateModule()) {
            return redirect()->route('subscription.index')
                ->with('error', 'You have reached your module limit. Please upgrade your plan to create more modules.');
        }

        return $next($request);
    }
}
