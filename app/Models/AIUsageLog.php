<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AIUsageLog extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'model',
        'feature',
        'prompt',
        'response',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'estimated_cost',
        'response_time_ms',
        'status',
        'error_message',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'prompt_tokens' => 'integer',
        'completion_tokens' => 'integer',
        'total_tokens' => 'integer',
        'estimated_cost' => 'decimal:6',
        'response_time_ms' => 'integer',
    ];

    /**
     * Get the user that made the request
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted cost in USD
     */
    public function getFormattedCostAttribute()
    {
        return '$' . number_format($this->estimated_cost, 4);
    }

    /**
     * Get formatted response time
     */
    public function getFormattedResponseTimeAttribute()
    {
        if ($this->response_time_ms < 1000) {
            return $this->response_time_ms . 'ms';
        }
        return round($this->response_time_ms / 1000, 2) . 's';
    }

    /**
     * Scope to filter successful requests
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope to filter by provider
     */
    public function scopeByProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Scope to filter by feature
     */
    public function scopeByFeature($query, $feature)
    {
        return $query->where('feature', $feature);
    }

    /**
     * Get total cost for a user
     */
    public static function getTotalCostForUser($userId, $days = 30)
    {
        return self::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->sum('estimated_cost');
    }

    /**
     * Get usage statistics
     */
    public static function getStatistics($days = 30)
    {
        $logs = self::where('created_at', '>=', now()->subDays($days));
        
        return [
            'total_requests' => $logs->count(),
            'successful_requests' => $logs->where('status', 'success')->count(),
            'total_tokens' => $logs->sum('total_tokens'),
            'total_cost' => $logs->sum('estimated_cost'),
            'avg_response_time' => $logs->avg('response_time_ms'),
        ];
    }
}
