<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'client_id', 'number', 'date',
        'order_reference', 'delivery_address', 'quote_reference', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
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
        return $this->hasMany(DeliveryItem::class)->orderBy('sort_order');
    }
}
