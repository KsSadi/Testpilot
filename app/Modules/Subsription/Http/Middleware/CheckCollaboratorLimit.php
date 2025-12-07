<?php

namespace App\Modules\Subsription\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCollaboratorLimit
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user->canShareWithMoreCollaborators()) {
            return redirect()->back()
                ->with('error', 'You have reached your collaborator limit. Please upgrade your plan to share with more people.');
        }

        return $next($request);
    }
}
