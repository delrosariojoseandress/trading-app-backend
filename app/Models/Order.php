<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'price',
        'quantity',
        'filled',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trades()
    {
        return $this->hasMany(Trade::class, 'buy_order_id')
                    ->orWhere('sell_order_id', $this->id);
    }
}