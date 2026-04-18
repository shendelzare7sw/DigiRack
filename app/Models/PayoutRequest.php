<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'amount',
        'fee',
        'net_amount',
        'status',
        'iris_reference_no',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
