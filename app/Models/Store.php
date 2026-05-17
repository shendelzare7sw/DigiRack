<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    /**
     * Regular expeditions whose cost is calculated automatically (RajaOngkir).
     * Keyed by the code used in checkout / OngkirController.
     */
    public const EXPEDITIONS = [
        'jne' => 'JNE',
        'pos' => 'POS Indonesia',
        'tiki' => 'TIKI',
    ];

    protected $fillable = [
        'user_id',
        'city_id',
        'name',
        'slug',
        'description',
        'enabled_expeditions',
        'logo',
        'banner',
        'identity_document_path',
        'identity_submitted_at',
        'is_active',
        'is_verified',
        'verification_status',
        'verification_notes',
        'verified_at',
        'avg_rating',
        'total_sold',
        'bank_name',
        'bank_account_no',
        'bank_account_name',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'avg_rating' => 'decimal:1',
            'identity_submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'enabled_expeditions' => 'array',
        ];
    }

    public function storeCouriers()
    {
        return $this->hasMany(StoreCourier::class);
    }

    /**
     * Regular expeditions the seller has switched on for this store,
     * as [code => label]. Empty until the seller configures couriers.
     */
    public function activeExpeditions(): array
    {
        $enabled = $this->enabled_expeditions ?? [];

        return collect(self::EXPEDITIONS)
            ->only($enabled)
            ->all();
    }

    /**
     * True when the store has at least one shipping option for buyers:
     * an active internal/instant courier OR an enabled regular expedition.
     */
    public function hasShippingOptions(): bool
    {
        if (! empty($this->activeExpeditions())) {
            return true;
        }

        return $this->storeCouriers()->where('is_active', true)->exists();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : asset('images/logo-digirack.png');
    }

    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner ? asset('storage/' . $this->banner) : null;
    }

    public function getIdentityDocumentUrlAttribute(): ?string
    {
        return $this->identity_document_path ? asset('storage/' . $this->identity_document_path) : null;
    }

    public function isApproved(): bool
    {
        return $this->verification_status === 'approved' && $this->is_verified;
    }
}
