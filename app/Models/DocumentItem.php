<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentItem extends Model
{
    protected $fillable = [
        'document_type', 'document_id', 'ref', 'description',
        'unit', 'quantity', 'unit_price_ht', 'total_price_ht', 'sort_order', 'is_category',
    ];

    protected function casts(): array
    {
        return [
            'quantity'      => 'decimal:2',
            'unit_price_ht' => 'decimal:2',
            'total_price_ht'=> 'decimal:2',
            'is_category'   => 'boolean',
        ];
    }

    public function document()
    {
        return $this->morphTo();
    }
}
