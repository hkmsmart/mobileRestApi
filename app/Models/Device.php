<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'devices';

    protected $fillable = [
        'uid','device_id', 'app_id','language','os'
    ];

}
