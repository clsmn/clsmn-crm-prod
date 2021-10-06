<?php

namespace App\Events\Backend\Lead;

use Illuminate\Queue\SerializesModels;

/**
 * Class LeadNoteAdded.
 */
class LeadNoteAdded
{
    use SerializesModels;

    /**
     * @var
     */
    public $lead;
    public $user;
    public $leadNote;

    /**
     * @param $lead
     */
    public function __construct($lead, $user, $leadNote)
    {
        $this->lead = $lead;
        $this->user = $user;
        $this->leadNote = $leadNote;
    }
}
