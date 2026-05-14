<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MarketAttachment extends Model
{
    protected $fillable = ['market_id', 'file_path', 'original_name'];

    public function market()
    {
        return $this->belongsTo(Market::class);
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('manager.market-attachments.download', $this->id);
    }

    public function getExtensionAttribute(): string
    {
        return strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
    }

    public function delete(): bool|null
    {
        Storage::disk('local')->delete($this->file_path);
        return parent::delete();
    }
}
