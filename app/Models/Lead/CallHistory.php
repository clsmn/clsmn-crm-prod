<?php

namespace App\Models\Lead;

use Illuminate\Database\Eloquent\Model;
use App\Models\Lead\Traits\Relationship\CallHistoryRelationship;

/**
 * Class CallHistory.
 */
class CallHistory extends Model
{
    use CallHistoryRelationship;
    /**
     * @var array
     */
    protected $dates = ['schedule_time', 'next_follow_up'];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('access.call_history_table');
    }

    function lead()
    {
        return $this->belongsTo(\App\Models\Lead\Lead::class, 'lead_id', 'id');
    }
}
