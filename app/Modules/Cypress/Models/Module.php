<?php

namespace App\Modules\Cypress\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasHashedRouteKey;
use App\Traits\Shareable;

class Module extends Model
{
    use HasFactory, HasHashedRouteKey, Shareable;

    protected $fillable = [
        'project_id',
        'created_by',
        'name',
        'description',
        'order',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($module) {
            if (empty($module->created_by) && auth()->check()) {
                $module->created_by = auth()->id();
            }
        });
    }

    /**
     * Get the project that owns the module
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user who created the module
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Get all test cases for this module
     */
    public function testCases()
    {
        return $this->hasMany(TestCase::class)->orderBy('order');
    }

    /**
     * Scope active modules
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope inactive modules
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
