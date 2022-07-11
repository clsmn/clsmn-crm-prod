<?php

namespace App\Models\Facebook;

use Illuminate\Database\Eloquent\Model;

class FacebookCmpAds extends Model
{
    protected $table = 'facebook_campaign_ads';

    public function campaign() 
    {
             return $this->hasOne('App\Models\Facebook\FacebookLeads','campaign_id','campaign_id');
    }
}
