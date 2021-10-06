<?php

namespace App\Models\Lead\Traits\Relationship;

/**
 * Class LeadRelationship.
 */
trait LeadRelationship
{
    /**
     * Ony-to-Many relations with Alternate Number.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function alternateNumbers()
    {
        return $this->hasMany(\App\Models\AlternateNumber\AlternateNumber::class, 'lead_id', 'id');
    }

    /**
     * Ony-to-Many relations with Child.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function children()
    {
        return $this->hasMany(\App\Models\Data\DataChild::class, 'data_user_id', 'data_user_id');
    }

    /**
     * Ony-to-Many relations with Call History.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function calls()
    {
        return $this->hasMany(\App\Models\Lead\CallHistory::class, 'lead_id', 'id');
    }

    /**
     * Ony-to-One relations with User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function assigned_executive()
    {
        return $this->hasOne(\App\Models\Access\User\User::class, 'id', 'assigned_to');
    }

}
