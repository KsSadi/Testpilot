<?php

namespace App\Traits;

use Vinkla\Hashids\Facades\Hashids;

trait HasHashedRouteKey
{
    /**
     * Get the route key for the model (returns hashid instead of plain id)
     */
    public function getRouteKey()
    {
        return Hashids::encode($this->getKey());
    }

    /**
     * Retrieve the model for a bound value (decodes hashid to find model)
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // Try to decode the hashid
        $decoded = Hashids::decode($value);
        
        // If decode fails or returns empty, return null (404)
        if (empty($decoded)) {
            return null;
        }
        
        $id = $decoded[0];
        
        // If model has created_by, filter by current user (multi-tenant)
        if (in_array('created_by', $this->getFillable()) && auth()->check()) {
            return $this->where($this->getKeyName(), $id)
                ->where('created_by', auth()->id())
                ->first();
        }
        
        // Otherwise just find by id
        return $this->where($this->getKeyName(), $id)->first();
    }

    /**
     * Get the hashid for this model
     */
    public function hashid()
    {
        return Hashids::encode($this->getKey());
    }
}
