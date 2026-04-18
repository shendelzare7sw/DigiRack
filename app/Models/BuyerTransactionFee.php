<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyerTransactionFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'amount' => 'decimal:2',
        ];
    }
}
