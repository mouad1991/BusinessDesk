<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'client_id', 'number', 'date', 'marche_number',
        'period_start', 'period_end', 'payment_mode', 'subject', 'tva_rate',
        'discount_percent', 'total_ht_before_discount', 'discount_amount',
        'total_ht', 'tva_amount', 'total_ttc', 'amount_in_words',
        'source_type', 'source_id', 'status',
        'paid_amount', 'payment_status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'date'                     => 'date',
            'period_start'             => 'date',
            'period_end'               => 'date',
            'tva_rate'                 => 'decimal:2',
            'discount_percent'         => 'decimal:2',
            'total_ht_before_discount' => 'decimal:2',
            'discount_amount'          => 'decimal:2',
            'total_ht'                 => 'decimal:2',
            'tva_amount'               => 'decimal:2',
            'total_ttc'                => 'decimal:2',
            'paid_amount'              => 'decimal:2',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(DocumentItem::class, 'document_id')
                    ->where('document_type', 'invoice')
                    ->orderBy('sort_order');
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class)->orderBy('payment_date');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->total_ttc - (float) $this->paid_amount);
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'partial' => 'Partiellement payée',
            'paid'    => 'Payée',
            default   => 'Non payée',
        };
    }

    // ─── Business logic ───────────────────────────────────────────────────────

    /**
     * Recompute paid_amount and payment_status from the payments table.
     * Call after any payment create / update / delete.
     */
    public function syncPaymentStatus(): void
    {
        $paidAmount = (float) $this->payments()->sum('amount');
        $total      = (float) $this->total_ttc;

        if ($total > 0 && $paidAmount >= $total) {
            $status = 'paid';
        } elseif ($paidAmount > 0) {
            $status = 'partial';
        } else {
            $status = 'unpaid';
        }

        $this->update([
            'paid_amount'    => $paidAmount,
            'payment_status' => $status,
        ]);
    }
}
