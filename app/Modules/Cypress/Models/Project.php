<?php

namespace App\Modules\Cypress\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Traits\HasHashedRouteKey;
use App\Traits\Shareable;

class Project extends Model
{
    use HasFactory, HasHashedRouteKey, Shareable;

    protected $fillable = [
        'name',
        'description',
        'logo',
        'url',
        'status',
        'created_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->created_by) && auth()->check()) {
                $project->created_by = auth()->id();
            }
        });
    }

    /**
     * Get the user who created the project
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all modules for this project
     */
    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    /**
     * Get all test cases for this project
     */
    public function testCases()
    {
        return $this->hasMany(TestCase::class)->orderBy('order');
    }

    /**
     * Scope active projects
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope inactive projects
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
