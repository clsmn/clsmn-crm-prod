<?php

namespace App\Models\Sales;


use Illuminate\Database\Eloquent\Model;
use Eloquent;
class DelightSales extends Model
{
    protected $table = 'delight_sales';

    public function user() 
    {
             return $this->hasOne('App\Models\Access\User\User','id','assigned_to');
    }
}
