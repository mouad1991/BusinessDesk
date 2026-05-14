<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'address', 'phone', 'email', 'rc', 'ice',
        'if_number', 'tp', 'capital', 'logo', 'doc_prefix',
        'bank_account_name', 'bank', 'rib', 'conditions_devis', 'conditions_bc', 'pdf_template',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function markets()
    {
        return $this->hasMany(Market::class);
    }

    public function deliveryNotes()
    {
        return $this->hasMany(DeliveryNote::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function documentCounters()
    {
        return $this->hasMany(DocumentCounter::class);
    }

    public function serviceDescriptions()
    {
        return $this->hasMany(ServiceDescription::class);
    }

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }
}
