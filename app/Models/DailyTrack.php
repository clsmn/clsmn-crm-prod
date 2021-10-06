<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DailyTrack.
 */
class DailyTrack extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'daily_track';

    public $timestamps = false;
}
