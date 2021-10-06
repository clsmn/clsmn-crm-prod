<?php

namespace App\Events\Backend\Lead;

use Illuminate\Queue\SerializesModels;

/**
 * Class LeadOpened.
 */
class LeadOpened
{
    use SerializesModels;

    /**
     * @var
     */
    public $lead;
    public $user;

    /**
     * @param $lead
     */
    public function __construct($lead, $user)
    {
        $this->lead = $lead;
        $this->user = $user;
    }
}
