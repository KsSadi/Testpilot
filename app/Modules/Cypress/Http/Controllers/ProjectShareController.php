<?php

namespace App\Modules\Cypress\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProjectShare;
use App\Models\User;
use App\Modules\Cypress\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class ProjectShareController extends Controller
{
    /**
     * Share project with a user
     */
    public function store(Request $request, $projectId)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'role' => 'required|in:editor,viewer',
            ]);

            $project = Project::findOrFail($projectId);

        // Check if user is owner
        if ($project->created_by !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Only project owner can share this project.'
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
                'message' => 'You cannot share project with yourself.'
            ], 400);
        }

        // Check if already shared
        $existingShare = ProjectShare::where('shareable_type', 'App\\Modules\\Cypress\\Models\\Project')
            ->where('shareable_id', $project->id)
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
                    'message' => 'Project is already shared with this user.'
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
                'shareable_type' => 'App\\Modules\\Cypress\\Models\\Project',
                'shareable_id' => $project->id,
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
            \Log::error('Failed to send project invitation email', [
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
        } catch (\Exception $e) {
            \Log::error('Project share error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all collaborators for a project
     */
    public function index($projectId)
    {
        $project = Project::findOrFail($projectId);

        // Check permission
        if (!$project->canView(auth()->id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $shares = ProjectShare::where('shareable_type', 'App\\Modules\\Cypress\\Models\\Project')
            ->where('shareable_id', $project->id)
            ->with(['sharedWith', 'sharedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'collaborators' => $shares,
            'is_owner' => $project->created_by === auth()->id()
        ]);
    }

    /**
     * Remove a collaborator
     */
    public function destroy($projectId, $shareId)
    {
        $project = Project::findOrFail($projectId);

        // Only owner can remove collaborators
        if ($project->created_by !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Only project owner can remove collaborators.'
            ], 403);
        }

        $share = ProjectShare::findOrFail($shareId);

        // Verify share belongs to this project
        if ($share->shareable_type != 'App\\Modules\\Cypress\\Models\\Project' || $share->shareable_id != $project->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid share.'
            ], 400);
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
            'message' => 'Invitation accepted! Project added to your list.',
            'project' => $share->shareable
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
    public function updateRole(Request $request, $projectId, $shareId)
    {
        $request->validate([
            'role' => 'required|in:editor,viewer'
        ]);

        $project = Project::findOrFail($projectId);

        // Only owner can update roles
        if ($project->created_by !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Only project owner can update roles.'
            ], 403);
        }

        $share = ProjectShare::findOrFail($shareId);

        if ($share->shareable_type != 'App\\Modules\\Cypress\\Models\\Project' || $share->shareable_id != $project->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid share.'
            ], 400);
        }

        $share->update(['role' => $request->role]);

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully.',
            'share' => $share->load('sharedWith')
        ]);
    }
}
