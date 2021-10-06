<?php

namespace App\Models\History\Traits\Relationship;

use App\Models\Lead\LeadNote;
use App\Models\Lead\CallRecord;
use App\Models\Access\User\User;
use App\Models\Lead\CallHistory;
use App\Models\History\HistoryType;

/**
 * Class HistoryRelationship.
 */
trait HistoryRelationship
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function type()
    {
        return $this->hasOne(HistoryType::class, 'id', 'type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function call()
    {
        return $this->hasOne(CallHistory::class, 'id', 'sub_entity_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function call_record()
    {
        return $this->hasOne(CallRecord::class, 'id', 'sub_entity_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function note()
    {
        return $this->hasOne(LeadNote::class, 'id', 'sub_entity_id');
    }
}
