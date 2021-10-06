<?php

namespace App\Events\Backend\Lead;

use Illuminate\Queue\SerializesModels;

/**
 * Class LeadUnattachedCall.
 */
class LeadUnattachedCall
{
    use SerializesModels;

    /**
     * @var
     */
    public $user;
    public $callRecord;

    /**
     * @param $lead
     */
    public function __construct($callRecord, $user)
    {
        $this->callRecord = $callRecord;
        $this->user = $user;
    }
}
