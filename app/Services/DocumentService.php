<?php

namespace App\Services;

use App\Models\Company;
use App\Models\DocumentItem;
use App\Models\ServiceDescription;

class DocumentService
{
    public function __construct(
        private NumberingService $numbering,
        private AmountInWordsService $amountInWords
    ) {}

    public function calculateTotals(array $items, float $tvaRate, ?float $discountPercent): array
    {
        $totalHtBeforeDiscount = 0;
        foreach ($items as $item) {
            if (!empty($item['is_category'])) continue;
            $totalHtBeforeDiscount += (float)($item['total_price_ht'] ?? 0);
        }

        $discountAmount = 0;
        if ($discountPercent && $discountPercent > 0) {
            $discountAmount = round($totalHtBeforeDiscount * $discountPercent / 100, 2);
        }

        $totalHt = round($totalHtBeforeDiscount - $discountAmount, 2);
        $tvaAmount = round($totalHt * $tvaRate / 100, 2);
        $totalTtc = round($totalHt + $tvaAmount, 2);

        return [
            'total_ht_before_discount' => $totalHtBeforeDiscount,
            'discount_amount'          => $discountAmount,
            'total_ht'                 => $totalHt,
            'tva_amount'               => $tvaAmount,
            'total_ttc'                => $totalTtc,
            'amount_in_words'          => $this->amountInWords->convert($totalTtc),
        ];
    }

    public function saveItems(string $docType, int $docId, array $items, Company $company): void
    {
        // Delete existing
        DocumentItem::where('document_type', $docType)
                    ->where('document_id', $docId)
                    ->delete();

        $order = 1;
        foreach ($items as $i => $item) {
            if (empty($item['description'])) continue;

            $isCategory = !empty($item['is_category']);
            $description = strip_tags($item['description'], '<strong><b><em><i><u><br><ul><ol><li><p><div><span>');

            if ($isCategory) {
                DocumentItem::create([
                    'document_type'  => $docType,
                    'document_id'    => $docId,
                    'ref'            => '',
                    'description'    => strip_tags($description), // plain text for category titles
                    'unit'           => '',
                    'quantity'       => 0,
                    'unit_price_ht'  => 0,
                    'total_price_ht' => 0,
                    'sort_order'     => $order,
                    'is_category'    => true,
                ]);
                $order++;
                continue;
            }

            $ref = str_pad($order, 2, '0', STR_PAD_LEFT);
            $qty = (float)($item['quantity'] ?? 1);
            $phu = (float)($item['unit_price_ht'] ?? 0);
            $pth = round($qty * $phu, 2);

            DocumentItem::create([
                'document_type'  => $docType,
                'document_id'    => $docId,
                'ref'            => $ref,
                'description'    => $description,
                'unit'           => $item['unit'] ?? 'F',
                'quantity'       => $qty,
                'unit_price_ht'  => $phu,
                'total_price_ht' => $pth,
                'sort_order'     => $order,
                'is_category'    => false,
            ]);

            $this->saveServiceDescription($company, strip_tags($description));
            $order++;
        }
    }

    private function saveServiceDescription(Company $company, string $description): void
    {
        $desc = ServiceDescription::where('company_id', $company->id)
            ->where('description', $description)
            ->first();

        if ($desc) {
            $desc->increment('usage_count');
        } else {
            ServiceDescription::create([
                'company_id'  => $company->id,
                'description' => $description,
            ]);
        }
    }
}
