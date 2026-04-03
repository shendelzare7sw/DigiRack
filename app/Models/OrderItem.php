<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name_snapshot',
        'price_snapshot',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'price_snapshot' => 'integer',
            'quantity' => 'integer',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute(): int
    {
        return $this->price_snapshot * $this->quantity;
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price_snapshot, 0, ',', '.');
    }
}
