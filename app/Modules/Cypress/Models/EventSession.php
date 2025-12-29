<?php

namespace App\Modules\Cypress\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasHashedRouteKey;
use Carbon\Carbon;

class EventSession extends Model
{
    use HasFactory, HasHashedRouteKey;

    protected $fillable = [
        'test_case_id',
        'session_uuid',
        'name',
        'version',
        'events_count',
        'recorded_at',
    ];

    protected $casts = [
        'events_count' => 'integer',
        'version' => 'integer',
        'recorded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the test case that owns this event session
     */
    public function testCase()
    {
        return $this->belongsTo(TestCase::class);
    }

    /**
     * Get the events for this session
     */
    public function events()
    {
        return $this->hasMany(TestCaseEvent::class, 'event_session_id');
    }

    /**
     * Get the generated codes created from this session
     */
    public function generatedCodes()
    {
        return $this->hasMany(GeneratedCode::class, 'event_session_id');
    }

    /**
     * Get formatted version label
     */
    public function getVersionLabelAttribute()
    {
        return 'Session v' . $this->version;
    }

    /**
     * Get formatted recorded_at timestamp
     */
    public function getFormattedRecordedAtAttribute()
    {
        return $this->recorded_at ? $this->recorded_at->format('M d, Y h:i A') : $this->created_at->format('M d, Y h:i A');
    }

    /**
     * Get short time label
     */
    public function getTimeAgoAttribute()
    {
        $date = $this->recorded_at ?? $this->created_at;
        return $date->diffForHumans();
    }

    /**
     * Generate next version number for a test case
     */
    public static function getNextVersion($testCaseId)
    {
        $maxVersion = static::where('test_case_id', $testCaseId)->max('version');
        return ($maxVersion ?? 0) + 1;
    }

    /**
     * Scope to get sessions for a test case
     */
    public function scopeForTestCase($query, $testCaseId)
    {
        return $query->where('test_case_id', $testCaseId);
    }

    /**
     * Scope to get latest sessions first
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('version', 'desc');
    }
}
