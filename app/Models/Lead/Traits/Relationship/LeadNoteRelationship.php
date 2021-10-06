<?php

namespace App\Models\Lead\Traits\Relationship;

/**
 * Class LeadNoteRelationship.
 */
trait LeadNoteRelationship
{
    /**
     * Ony-to-One relations with User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function user()
    {
        return $this->hasOne(\App\Models\Access\User\User::class, 'id', 'user_id');
    }
}
