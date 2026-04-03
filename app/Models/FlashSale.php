<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'discount_percent',
        'original_price',
        'sale_price',
        'stock_flash',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'original_price' => 'integer',
            'sale_price' => 'integer',
            'is_active' => 'boolean',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function isOngoing(): bool
    {
        return $this->is_active
            && $this->start_time <= now()
            && $this->end_time >= now();
    }

    public function getFormattedSalePriceAttribute(): string
    {
        return 'Rp ' . number_format($this->sale_price, 0, ',', '.');
    }

    public function getFormattedOriginalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->original_price, 0, ',', '.');
    }
}
