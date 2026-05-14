<?php

namespace App\Services;

use App\Models\Company;
use App\Models\DocumentCounter;
use Illuminate\Support\Facades\DB;

class NumberingService
{
    // Type codes: D=Devis, F=Facture, BL=Bon de livraison, BC=Bon de commande
    public function generate(Company $company, string $type): string
    {
        return DB::transaction(function () use ($company, $type) {
            $year = (int) date('Y');
            $shortYear = date('y'); // 26

            $counter = DocumentCounter::lockForUpdate()
                ->firstOrCreate(
                    ['company_id' => $company->id, 'type' => $type, 'year' => $year],
                    ['last_number' => 0]
                );

            $counter->increment('last_number');
            $counter->refresh();

            $seq = str_pad($counter->last_number, 3, '0', STR_PAD_LEFT);

            // Format: PREFIX-TYPE-001/26
            return "{$company->doc_prefix}-{$type}-{$seq}/{$shortYear}";
        });
    }
}
