<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public function generateQuote($quote): \Barryvdh\DomPDF\PDF
    {
        $quote->load(['company', 'client', 'items']);
        return $this->build('pdf.devis', $quote->company, [
            'document' => $quote, 'docAccentColor' => '#1a73c8',
        ]);
    }

    public function generateInvoice($invoice): \Barryvdh\DomPDF\PDF
    {
        $invoice->load(['company', 'client', 'items', 'payments']);
        return $this->build('pdf.facture', $invoice->company, [
            'document' => $invoice, 'docAccentColor' => '#1a73c8', 'sigAccentColor' => '#e8a020',
        ]);
    }

    public function generateDeliveryNote($note): \Barryvdh\DomPDF\PDF
    {
        $note->load(['company', 'client', 'items']);
        return $this->build('pdf.bon_livraison', $note->company, [
            'document' => $note, 'docAccentColor' => '#c048c8',
        ]);
    }

    public function generatePurchaseOrder($order): \Barryvdh\DomPDF\PDF
    {
        $order->load(['company', 'client', 'items']);
        return $this->build('pdf.bon_commande', $order->company, [
            'document' => $order, 'docAccentColor' => '#1a73c8',
        ]);
    }

    private function build(string $view, $company, array $data): \Barryvdh\DomPDF\PDF
    {
        $data['pdfTheme'] = $company->pdf_template ?? 'modern';

        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->set_option('isHtml5ParserEnabled', true);
        $pdf->set_option('isRemoteEnabled', true);
        $pdf->set_option('defaultFont', 'DejaVu Sans');
        return $pdf;
    }
}
