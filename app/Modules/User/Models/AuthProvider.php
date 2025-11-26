<?php

namespace App\Modules\User\Models;

use Illuminate\Database\Eloquent\Model;

class AuthProvider extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'provider_token',
        'provider_refresh_token',
    ];

    /**
     * Get the user that owns the auth provider.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
