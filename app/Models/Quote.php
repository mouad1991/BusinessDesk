<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'client_id', 'number', 'date', 'validity_days',
        'payment_mode', 'subject', 'tva_rate', 'discount_percent',
        'total_ht_before_discount', 'discount_amount', 'total_ht',
        'tva_amount', 'total_ttc', 'amount_in_words', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'date'                    => 'date',
            'tva_rate'                => 'decimal:2',
            'discount_percent'        => 'decimal:2',
            'total_ht_before_discount'=> 'decimal:2',
            'discount_amount'         => 'decimal:2',
            'total_ht'                => 'decimal:2',
            'tva_amount'              => 'decimal:2',
            'total_ttc'               => 'decimal:2',
        ];
    }

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
                    ->where('document_type', 'quote')
                    ->orderBy('sort_order');
    }
}
