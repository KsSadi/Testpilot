<?php

namespace App\Modules\Cypress\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProjectShare;
use App\Models\User;
use App\Modules\Cypress\Models\Project;
use App\Modules\Cypress\Models\Module;
use App\Modules\Cypress\Models\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ShareController extends Controller
{
    /**
     * Share any entity (project/module/test case) with a user
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:editor,viewer',
            'shareable_type' => 'required|in:project,module,testcase',
            'shareable_id' => 'required',
        ]);

        // Get the shareable entity
        $shareable = $this->getShareableEntity($request->shareable_type, $request->shareable_id);

        if (!$shareable) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found.'
            ], 404);
        }

        // Check if user is owner
        if ($shareable->created_by !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Only the owner can share this resource.'
            ], 403);
        }

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found. Please make sure they have an account.'
            ], 404);
        }

        // Check if trying to share with self
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot share with yourself.'
            ], 400);
        }

        // Check if already shared
        $existingShare = ProjectShare::where('shareable_type', get_class($shareable))
            ->where('shareable_id', $shareable->id)
            ->where('shared_with_user_id', $user->id)
            ->first();

        if ($existingShare) {
            if ($existingShare->status === ProjectShare::STATUS_PENDING) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invitation already sent to this user.'
                ], 400);
            }

            if ($existingShare->status === ProjectShare::STATUS_ACCEPTED) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already shared with this user.'
                ], 400);
            }

            // If rejected, allow re-sharing
            $existingShare->update([
                'status' => ProjectShare::STATUS_PENDING,
                'role' => $request->role,
                'rejected_at' => null,
            ]);

            $share = $existingShare;
        } else {
            // Create new share
            $share = ProjectShare::create([
                'shareable_type' => get_class($shareable),
                'shareable_id' => $shareable->id,
                'shared_with_user_id' => $user->id,
                'shared_by_user_id' => auth()->id(),
                'role' => $request->role,
                'status' => ProjectShare::STATUS_PENDING,
            ]);
        }

        // Send email notification (in background, don't block)
        try {
            $shareForEmail = ProjectShare::with(['shareable', 'sharedBy', 'sharedWith'])->find($share->id);
            Mail::to($user->email)->send(new \App\Mail\ProjectInvitation($shareForEmail));
        } catch (\Exception $e) {
            \Log::error('Failed to send invitation email', [
                'error' => $e->getMessage(),
                'share_id' => $share->id
            ]);
            // Continue anyway - invitation is created
        }

        return response()->json([
            'success' => true,
            'message' => 'Invitation sent successfully!',
            'share' => ProjectShare::with(['sharedWith', 'sharedBy'])->find($share->id)
        ]);
    }

    /**
     * Get all collaborators for an entity
     */
    public function index(Request $request)
    {
        $request->validate([
            'shareable_type' => 'required|in:project,module,testcase',
            'shareable_id' => 'required',
        ]);

        $shareable = $this->getShareableEntity($request->shareable_type, $request->shareable_id);

        if (!$shareable) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found.'
            ], 404);
        }

        // Check permission
        if (!$shareable->canView(auth()->id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $shares = ProjectShare::where('shareable_type', get_class($shareable))
            ->where('shareable_id', $shareable->id)
            ->with(['sharedWith', 'sharedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'collaborators' => $shares,
            'is_owner' => $shareable->created_by === auth()->id()
        ]);
    }

    /**
     * Remove a collaborator
     */
    public function destroy($shareId)
    {
        $share = ProjectShare::findOrFail($shareId);
        $shareable = $share->shareable;

        // Only owner can remove collaborators
        if ($shareable->created_by !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Only the owner can remove collaborators.'
            ], 403);
        }

        $share->delete();

        return response()->json([
            'success' => true,
            'message' => 'Collaborator removed successfully.'
        ]);
    }

    /**
     * Accept invitation
     */
    public function accept($shareId)
    {
        $share = ProjectShare::findOrFail($shareId);

        // Verify this invitation is for current user
        if ($share->shared_with_user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($share->status !== ProjectShare::STATUS_PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'This invitation has already been ' . $share->status . '.'
            ], 400);
        }

        $share->accept();

        return response()->json([
            'success' => true,
            'message' => 'Invitation accepted!',
            'shareable' => $share->shareable,
            'shareable_type' => $share->share_type
        ]);
    }

    /**
     * Reject invitation
     */
    public function reject($shareId)
    {
        $share = ProjectShare::findOrFail($shareId);

        // Verify this invitation is for current user
        if ($share->shared_with_user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($share->status !== ProjectShare::STATUS_PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'This invitation has already been ' . $share->status . '.'
            ], 400);
        }

        $share->reject();

        return response()->json([
            'success' => true,
            'message' => 'Invitation rejected.'
        ]);
    }

    /**
     * Get pending invitations for current user
     */
    public function pendingInvitations()
    {
        $invitations = ProjectShare::forUser(auth()->id())
            ->pending()
            ->with(['shareable', 'sharedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'invitations' => $invitations
        ]);
    }

    /**
     * Update collaborator role
     */
    public function updateRole(Request $request, $shareId)
    {
        $request->validate([
            'role' => 'required|in:editor,viewer'
        ]);

        $share = ProjectShare::findOrFail($shareId);
        $shareable = $share->shareable;

        // Only owner can update roles
        if ($shareable->created_by !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Only the owner can update roles.'
            ], 403);
        }

        $share->update(['role' => $request->role]);

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully.',
            'share' => $share->load('sharedWith')
        ]);
    }

    /**
     * Helper: Get shareable entity by type and ID
     */
    private function getShareableEntity($type, $id)
    {
        return match($type) {
            'project' => Project::find($id),
            'module' => Module::find($id),
            'testcase' => TestCase::find($id),
            default => null
        };
    }
}
