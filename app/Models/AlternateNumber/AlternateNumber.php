<?php

namespace App\Models\AlternateNumber;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AlternateNumber.
 */
class AlternateNumber extends Model
{
    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('access.alternate_number_table');
    }
}
