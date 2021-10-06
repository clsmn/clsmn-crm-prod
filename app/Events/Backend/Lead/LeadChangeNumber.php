<?php

namespace App\Events\Backend\Lead;

use Illuminate\Queue\SerializesModels;

/**
 * Class LeadChangeNumber.
 */
class LeadChangeNumber
{
    use SerializesModels;

    /**
     * @var
     */
    public $lead;
    public $oldNumber;


    /**
     * @param $lead
     */
    public function __construct($lead, $oldNumber)
    {
        $this->lead = $lead;
        $this->oldNumber = $oldNumber;
    }
}
