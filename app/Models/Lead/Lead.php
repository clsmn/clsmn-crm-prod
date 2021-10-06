<?php

namespace App\Models\Lead;

use Illuminate\Database\Eloquent\Model;
use App\Models\Lead\Traits\Attribute\LeadAttribute;
use App\Models\Lead\Traits\Relationship\LeadRelationship;


/**
 * Class Lead.
 */
class Lead extends Model
{
    use LeadRelationship, LeadAttribute;
    
    /**
     * @var array
     */
    protected $dates = ['last_call', 'next_follow_up'];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('access.leads_table');
    }
}
