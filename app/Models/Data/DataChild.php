<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class DataChild.
 */
class DataChild extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $dates = ['added_on', 'deleted_at', 'dob'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'data_child';

    /**
     * Accessor for Age.
     */
    public function getAgeAttribute()
    {
        if($this->dob != null)
        {
            return \Carbon\Carbon::parse($this->attributes['dob'])->age;
        }
    }
}
