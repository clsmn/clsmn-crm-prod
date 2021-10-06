<?php

namespace App\Models\Lead;

use Illuminate\Database\Eloquent\Model;
use App\Models\Lead\Traits\Relationship\LeadNoteRelationship;

/**
 * Class LeadNote.
 */
class LeadNote extends Model
{
    use LeadNoteRelationship;
    
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
        $this->table = 'lead_note';
    }
}
