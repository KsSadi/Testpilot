<?php

namespace App\Modules\Cypress\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasHashedRouteKey;

class GeneratedCode extends Model
{
    use HasFactory, HasHashedRouteKey;

    protected $fillable = [
        'test_case_id',
        'event_session_id',
        'code',
        'is_ai_generated',
        'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'is_ai_generated' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the test case that owns the generated code.
     */
    public function testCase()
    {
        return $this->belongsTo(TestCase::class);
    }

    /**
     * Get the event session this code was generated from
     */
    public function eventSession()
    {
        return $this->belongsTo(EventSession::class, 'event_session_id');
    }

    /**
     * Format the generated_at timestamp for display
     */
    public function getFormattedGeneratedAtAttribute()
    {
        return $this->generated_at->format('M d, Y h:i A');
    }

    /**
     * Get a short version label for display
     */
    public function getVersionLabelAttribute()
    {
        return 'v' . $this->generated_at->format('Y.m.d.His');
    }

    /**
     * Scope to get latest codes first
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('generated_at', 'desc');
    }
}
