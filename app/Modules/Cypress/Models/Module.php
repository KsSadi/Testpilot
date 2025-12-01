<?php

namespace App\Modules\Cypress\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'order',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the project that owns the module
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
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
