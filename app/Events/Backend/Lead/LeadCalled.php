<?php

namespace App\Events\Backend\Lead;

use Illuminate\Queue\SerializesModels;

/**
 * Class LeadCalled.
 */
class LeadCalled
{
    use SerializesModels;

    /**
     * @var
     */
    public $lead;
    public $user;
    public $callHistory;

    /**
     * @param $lead
     */
    public function __construct($lead, $user, $callHistory)
    {
        $this->lead = $lead;
        $this->user = $user;
        $this->callHistory = $callHistory;
    }
}
