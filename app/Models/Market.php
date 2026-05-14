<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Market extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'client_id', 'title', 'market_type', 'category',
        'start_date', 'end_date', 'status',
        'total_amount', 'invoiced_amount', 'paid_amount', 'remaining_amount',
        'caution_amount', 'caution_status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date'       => 'date',
            'end_date'         => 'date',
            'total_amount'     => 'decimal:2',
            'invoiced_amount'  => 'decimal:2',
            'paid_amount'      => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'caution_amount'   => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Market $market) {
            $market->remaining_amount = max(0, (float)$market->total_amount - (float)$market->paid_amount);
        });
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function company()    { return $this->belongsTo(Company::class); }
    public function client()     { return $this->belongsTo(Client::class); }
    public function attachments(){ return $this->hasMany(MarketAttachment::class); }

    // ─── Status helpers ───────────────────────────────────────────────────────

    public static function statusOptions(): array
    {
        return [
            'studying'    => 'À étudier',
            'applied'     => 'Postulé',
            'retained'    => 'Retenu',
            'in_progress' => 'En cours',
            'completed'   => 'Terminé',
            'lost'        => 'Perdu / Non retenu',
        ];
    }

    public static function statusBadge(): array
    {
        return [
            'studying'    => 'badge-gray',
            'applied'     => 'badge-orange',
            'retained'    => 'badge-blue',
            'in_progress' => 'badge-purple',
            'completed'   => 'badge-green',
            'lost'        => 'badge-red',
        ];
    }

    public static function typeOptions(): array
    {
        return ['public' => 'Public', 'private' => 'Privé'];
    }

    public static function categoryOptions(): array
    {
        return [
            'supply'      => 'Fourniture',
            'service'     => 'Service',
            'works'       => 'Travaux',
            'maintenance' => 'Maintenance',
            'other'       => 'Autre',
        ];
    }

    public static function cautionStatusOptions(): array
    {
        return [
            'not_deposited' => 'Non déposée',
            'deposited'     => 'Déposée',
            'recovered'     => 'Récupérée',
        ];
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return self::statusOptions()[$this->status] ?? $this->status;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return self::statusBadge()[$this->status] ?? 'badge-gray';
    }

    public function getMarketTypeLabelAttribute(): string
    {
        return self::typeOptions()[$this->market_type] ?? $this->market_type;
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categoryOptions()[$this->category] ?? $this->category;
    }

    public function getCautionStatusLabelAttribute(): ?string
    {
        if (!$this->caution_status) return null;
        return self::cautionStatusOptions()[$this->caution_status] ?? $this->caution_status;
    }
}
