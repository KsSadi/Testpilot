<?php

namespace App\Modules\Cypress\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestCaseEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
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

    public function testCase()
    {
        return $this->belongsTo(TestCase::class, 'session_id', 'session_id');
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
}
