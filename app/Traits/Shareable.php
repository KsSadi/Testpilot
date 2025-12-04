<?php

namespace App\Traits;

use App\Models\ProjectShare;
use App\Models\User;

trait Shareable
{
    /**
     * Get all shares for this entity
     */
    public function shares()
    {
        return $this->morphMany(ProjectShare::class, 'shareable');
    }

    /**
     * Get all collaborators (users who have access)
     */
    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'project_shares', 'shareable_id', 'shared_with_user_id')
            ->wherePivot('shareable_type', get_class($this))
            ->wherePivot('status', ProjectShare::STATUS_ACCEPTED)
            ->withPivot('role', 'status', 'created_at');
    }

    /**
     * Check if entity is owned by user
     */
    public function isOwnedBy($userId): bool
    {
        return $this->created_by == $userId;
    }

    /**
     * Check if entity is shared with user
     */
    public function isSharedWith($userId): bool
    {
        return $this->shares()
            ->where('shared_with_user_id', $userId)
            ->where('status', ProjectShare::STATUS_ACCEPTED)
            ->exists();
    }

    /**
     * Get user's role for this entity
     */
    public function getUserRole($userId): ?string
    {
        if ($this->isOwnedBy($userId)) {
            return ProjectShare::ROLE_OWNER;
        }

        $share = $this->shares()
            ->where('shared_with_user_id', $userId)
            ->where('status', ProjectShare::STATUS_ACCEPTED)
            ->first();

        return $share?->role;
    }

    /**
     * Check if user can edit
     */
    public function canEdit($userId): bool
    {
        $role = $this->getUserRole($userId);
        return in_array($role, [ProjectShare::ROLE_OWNER, ProjectShare::ROLE_EDITOR]);
    }

    /**
     * Check if user can view
     */
    public function canView($userId): bool
    {
        return $this->getUserRole($userId) !== null;
    }

    /**
     * Share with a user
     */
    public function shareWith(User $user, string $role = ProjectShare::ROLE_VIEWER, User $sharedBy = null)
    {
        return $this->shares()->create([
            'shared_with_user_id' => $user->id,
            'shared_by_user_id' => $sharedBy?->id ?? auth()->id(),
            'role' => $role,
            'status' => ProjectShare::STATUS_PENDING,
        ]);
    }
}
