<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectShare extends Model
{
    protected $fillable = [
        'shareable_type',
        'shareable_id',
        'shared_with_user_id',
        'shared_by_user_id',
        'role',
        'status',
        'accepted_at',
        'rejected_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // Role constants
    const ROLE_OWNER = 'owner';
    const ROLE_EDITOR = 'editor';
    const ROLE_VIEWER = 'viewer';

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the shareable entity (Project, Module, or TestCase)
     */
    public function shareable()
    {
        return $this->morphTo();
    }

    /**
     * Get the project - for backward compatibility
     */
    public function project()
    {
        if ($this->shareable_type === 'App\\Modules\\Cypress\\Models\\Project') {
            return $this->shareable();
        }
        
        // If sharing a module or test case, get the parent project
        return $this->shareable->project() ?? null;
    }

    /**
     * Get the user who is receiving the share
     */
    public function sharedWith()
    {
        return $this->belongsTo(User::class, 'shared_with_user_id');
    }

    /**
     * Get the user who created the share
     */
    public function sharedBy()
    {
        return $this->belongsTo(User::class, 'shared_by_user_id');
    }

    /**
     * Scope: Get pending invitations
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope: Get accepted shares
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    /**
     * Scope: Get shares for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('shared_with_user_id', $userId);
    }

    /**
     * Scope: Get shares for projects only
     */
    public function scopeProjects($query)
    {
        return $query->where('shareable_type', 'App\\Modules\\Cypress\\Models\\Project');
    }

    /**
     * Scope: Get shares for modules only
     */
    public function scopeModules($query)
    {
        return $query->where('shareable_type', 'App\\Modules\\Cypress\\Models\\Module');
    }

    /**
     * Scope: Get shares for test cases only
     */
    public function scopeTestCases($query)
    {
        return $query->where('shareable_type', 'App\\Modules\\Cypress\\Models\\TestCase');
    }

    /**
     * Get the share type display name
     */
    public function getShareTypeAttribute(): string
    {
        return match($this->shareable_type) {
            'App\\Modules\\Cypress\\Models\\Project' => 'Project',
            'App\\Modules\\Cypress\\Models\\Module' => 'Module',
            'App\\Modules\\Cypress\\Models\\TestCase' => 'Test Case',
            default => 'Unknown'
        };
    }

    /**
     * Accept the invitation
     */
    public function accept()
    {
        $this->update([
            'status' => self::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ]);
    }

    /**
     * Reject the invitation
     */
    public function reject()
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejected_at' => now(),
        ]);
    }

    /**
     * Check if user can edit
     */
    public function canEdit(): bool
    {
        return in_array($this->role, [self::ROLE_OWNER, self::ROLE_EDITOR]);
    }

    /**
     * Check if user can only view
     */
    public function canOnlyView(): bool
    {
        return $this->role === self::ROLE_VIEWER;
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayAttribute(): string
    {
        return match($this->role) {
            self::ROLE_OWNER => 'Owner',
            self::ROLE_EDITOR => 'Editor',
            self::ROLE_VIEWER => 'Viewer',
            default => ucfirst($this->role)
        };
    }
}
