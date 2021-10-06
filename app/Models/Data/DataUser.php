<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DataUser.
 */
class DataUser extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'data_user';

    /**
     * Ony-to-Many relations with Child.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function children()
    {
        return $this->hasMany(\App\Models\Data\DataChild::class, 'data_user_id', 'id');
    }

    function lead()
    {
        return $this->belongsTo(\App\Models\Lead\Lead::class, 'phone', 'phone');
    }
}
