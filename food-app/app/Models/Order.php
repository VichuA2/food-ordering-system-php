<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hotel_id',
        'delivery_person_id',
        'total_amount',
        'delivery_location',
        'status',
    ];

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
    public function deliveryPerson()
    {
        return $this->belongsTo(User::class, 'delivery_person_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function supportQueries()
    {
        return $this->hasMany(Query::class);
    }
}
