<?php

namespace App\Models\Lead\Traits\Attribute;

/**
 * Class LeadAttribute.
 */
trait LeadAttribute
{
    /**
     * @return string
     */
    public function getStatusClassAttribute()
    {
        $status = $this->lead_status;
        $class = '';
        if($status == 'sale')
        {
            $class = 'btn-success';
        }else if($status == 'hot')
        {
            $class = 'btn-primary';
        }else if($status == 'mild')
        {
            $class = 'btn-info';
        }else if($status == 'cold')
        {
            $class = 'btn-warning';
        }else if($status == 'dead')
        {
            $class = 'btn-danger';
        }
        return $class;

        return $class;
    }
}
