<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'buyer_id',
        'store_id',
        'status',
        'total_price',
        'shipping_cost',
        'payment_method',
        'payment_status',
        'payment_token',
        'payment_reference',
        'payment_proof',
        'shipping_address',
        'applied_buyer_fees',
        'shipping_tracking_number',
        'shipped_at',
        'delivered_at',
        'delivery_confirmation_note',
        'delivery_proof_path',
        'delivery_proof_paths',
        'cancellation_reason',
        'cancellation_response',
        'cancellation_requested_at',
        'cancellation_resolved_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'integer',
            'shipping_cost' => 'integer',
            'shipping_address' => 'array',
            'applied_buyer_fees' => 'array',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'delivery_proof_paths' => 'array',
            'cancellation_requested_at' => 'datetime',
            'cancellation_resolved_at' => 'datetime',
        ];
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function storeReview()
    {
        return $this->hasOne(StoreReview::class);
    }

    // Helpers
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending_payment' => 'Menunggu Pembayaran',
            'processing' => 'Diproses',
            'cancellation_requested' => 'Menunggu Persetujuan Batal',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending_payment' => 'yellow',
            'processing' => 'blue',
            'cancellation_requested' => 'orange',
            'shipped' => 'indigo',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }
}
