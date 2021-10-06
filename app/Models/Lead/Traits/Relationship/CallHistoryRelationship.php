<?php

namespace App\Models\Lead\Traits\Relationship;

/**
 * Class CallHistoryRelationship.
 */
trait CallHistoryRelationship
{
    /**
     * Ony-to-One relations with Child.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function lead()
    {
        return $this->hasOne(\App\Models\Lead\Lead::class, 'id', 'lead_id');
    }

    /**
     * Ony-to-One relations with Executive.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function user()
    {
        return $this->hasOne(\App\Models\Access\User\User::class, 'id', 'called_by');
    }

}
