<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceDescription extends Model
{
    protected $fillable = ['company_id', 'description', 'usage_count'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
