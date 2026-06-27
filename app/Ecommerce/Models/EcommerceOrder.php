<?php

namespace App\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EcommerceOrder extends Model
{
    protected $table = 'ecommerce_orders';

    protected $fillable = [
        'customer_id', 'order_number', 'status', 'payment_status',
        'payment_method', 'subtotal', 'shipping_cost', 'discount', 'total',
        'shipping_name', 'shipping_phone', 'shipping_address',
        'shipping_city', 'shipping_province', 'shipping_postal',
        'notes', 'paid_at', 'shipped_at', 'delivered_at',
    ];

    protected $casts = [
        'subtotal'     => 'integer',
        'shipping_cost'=> 'integer',
        'discount'     => 'integer',
        'total'        => 'integer',
        'paid_at'      => 'datetime',
        'shipped_at'   => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(EcommerceCustomer::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(EcommerceOrderItem::class, 'order_id');
    }

    public function getTotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'Menunggu',
            'processing' => 'Diproses',
            'shipped'    => 'Dikirim',
            'delivered'  => 'Selesai',
            'cancelled'  => 'Dibatalkan',
            'refunded'   => 'Refund',
            default      => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'warning',
            'processing' => 'info',
            'shipped'    => 'brand',
            'delivered'  => 'success',
            'cancelled',
            'refunded'   => 'error',
            default      => 'default',
        };
    }

    // Generate order number
    public static function generateOrderNumber(): string
    {
        return 'ORD-' . strtoupper(now()->format('Ymd')) . '-' . str_pad(
            (static::whereDate('created_at', today())->count() + 1),
            4, '0', STR_PAD_LEFT
        );
    }
}