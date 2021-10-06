<?php

namespace App\Models\Lead\Traits\Relationship;

/**
 * Class CallRecordRelationship.
 */
trait CallRecordRelationship
{
    /**
     * Ony-to-One relations with Lead.
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
        return $this->hasOne(\App\Models\Access\User\User::class, 'id', 'user_id');
    }

}
