<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RepitchAssigned.
 */
class RepitchAssigned extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'repitch_assigned';

    /**
     * Ony-to-Many relations with Child.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
   
}
