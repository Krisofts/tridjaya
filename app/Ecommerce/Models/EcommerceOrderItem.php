<?php

namespace App\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcommerceOrderItem extends Model
{
    protected $table = 'ecommerce_order_items';

    protected $fillable = [
        'order_id', 'product_id', 'name', 'sku',
        'price', 'qty', 'subtotal',
    ];

    protected $casts = [
        'price'    => 'integer',
        'qty'      => 'integer',
        'subtotal' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(EcommerceOrder::class, 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(EcommerceProduct::class, 'product_id');
    }
}