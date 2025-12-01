<?php

namespace App\Modules\Cypress\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'module_id',
        'name',
        'description',
        'order',
        'session_id',
        'session_data',
        'status'
    ];

    protected static function boot()
    {
        parent::boot();

        // Generate unique session_id when creating test case
        static::creating(function ($testCase) {
            if (empty($testCase->session_id)) {
                $testCase->session_id = 'tc_' . time() . '_' . uniqid();
            }
        });
    }

    protected $casts = [
        'session_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function events()
    {
        return $this->hasMany(TestCaseEvent::class, 'session_id', 'session_id');
    }

    public function savedEvents()
    {
        return $this->hasMany(TestCaseEvent::class, 'session_id', 'session_id')
                    ->where('is_saved', true);
    }

    public function unsavedEvents()
    {
        return $this->hasMany(TestCaseEvent::class, 'session_id', 'session_id')
                    ->where('is_saved', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function previousTestCase()
    {
        $query = self::where('project_id', $this->project_id);

        if ($this->module_id) {
            $query->where('module_id', $this->module_id);
        }

        return $query->where('order', '<', $this->order)
            ->orderBy('order', 'desc')
            ->first();
    }

    public function nextTestCase()
    {
        $query = self::where('project_id', $this->project_id);

        if ($this->module_id) {
            $query->where('module_id', $this->module_id);
        }

        return $query->where('order', '>', $this->order)
            ->orderBy('order', 'asc')
            ->first();
    }
}
