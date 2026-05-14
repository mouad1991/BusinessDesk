<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryItem extends Model
{
    protected $fillable = [
        'delivery_note_id', 'ref', 'description', 'unit',
        'qty_ordered', 'qty_shipped', 'qty_pending', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'qty_ordered' => 'decimal:2',
            'qty_shipped' => 'decimal:2',
            'qty_pending' => 'decimal:2',
        ];
    }

    public function deliveryNote()
    {
        return $this->belongsTo(DeliveryNote::class);
    }
}
