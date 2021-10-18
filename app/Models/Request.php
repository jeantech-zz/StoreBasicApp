<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $fillable = [
        'order_id',
        'url_app',
        'url_payment',
        'response',
        'requestId',        	
        'expiration',
        'reference',

    ];

    use HasFactory;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function order()
    {
        return $this->hasOne('App\Models\Order', 'id', 'order_id');
    }
}


