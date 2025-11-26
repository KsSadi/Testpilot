<?php

namespace App\Modules\Cypress\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'order',
        'session_data',
        'status'
    ];

    protected $casts = [
        'session_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function previousTestCase()
    {
        return self::where('project_id', $this->project_id)
            ->where('order', '<', $this->order)
            ->orderBy('order', 'desc')
            ->first();
    }

    public function nextTestCase()
    {
        return self::where('project_id', $this->project_id)
            ->where('order', '>', $this->order)
            ->orderBy('order', 'asc')
            ->first();
    }
}
