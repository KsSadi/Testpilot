<?php

namespace App\Modules\Cypress\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestCaseEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'event_session_id',
        'event_type',
        'selector',
        'tag_name',
        'url',
        'value',
        'inner_text',
        'attributes',
        'event_data',
        'is_saved'
    ];

    protected $casts = [
        'attributes' => 'array',
        'event_data' => 'array',
        'is_saved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the test case (via session_id - legacy support)
     */
    public function testCase()
    {
        return $this->belongsTo(TestCase::class, 'session_id', 'session_id');
    }

    /**
     * Get the event session this event belongs to
     */
    public function eventSession()
    {
        return $this->belongsTo(EventSession::class, 'event_session_id');
    }

    public function scopeSaved($query)
    {
        return $query->where('is_saved', true);
    }

    public function scopeUnsaved($query)
    {
        return $query->where('is_saved', false);
    }

    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeByEventSession($query, $eventSessionId)
    {
        return $query->where('event_session_id', $eventSessionId);
    }
}
