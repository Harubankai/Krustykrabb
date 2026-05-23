<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_id', 'customer_id', 'rider_id', 
        'total', 'total_items', 'payment_method', 
        'status', 'delivery_photo', 'delivery_step', 
        'accepted_at', 'picked_up_at', 'in_transit_at', 'arrived_at', 'completed_at'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function rider()
    {
        return $this->belongsTo(User::class, 'rider_id');
    }
}
