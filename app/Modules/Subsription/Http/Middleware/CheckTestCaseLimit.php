<?php

namespace App\Modules\Subsription\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTestCaseLimit
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user->canCreateTestCase()) {
            return redirect()->route('subscription.index')
                ->with('error', 'You have reached your test case limit. Please upgrade your plan to create more test cases.');
        }

        return $next($request);
    }
}
