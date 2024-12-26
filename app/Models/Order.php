<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_type',
        'table_number',
        'portions',
        'status',
        'payment_method',
    ];

    protected $casts = [
        'portions' => 'array',
    ];

    // Model Order.php
public function payment()
{
    return $this->hasOne(Payment::class);
}

}
