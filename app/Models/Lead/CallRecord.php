<?php

namespace App\Models\Lead;

use Illuminate\Database\Eloquent\Model;
use App\Models\Lead\Traits\Relationship\CallRecordRelationship;

/**
 * Class CallRecord.
 */
class CallRecord extends Model
{
    use CallRecordRelationship;

    /**
     * @var array
     */
    protected $dates = [];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = 'call_records';
    }
}
