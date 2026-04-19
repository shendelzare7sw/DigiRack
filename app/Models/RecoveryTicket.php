<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecoveryTicket extends Model
{
    protected $fillable = [
        'user_id', 'tipe_recovery', 'status', 'token_reset', 'expires_at', 'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
