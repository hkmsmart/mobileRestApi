<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SubscriberStatu extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'subscriber_status';

    protected $fillable = [
        'uid','device_id', 'statu','receipt','start_date'
    ];

}
