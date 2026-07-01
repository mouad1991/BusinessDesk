<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\PurchaseOrder;
use App\Services\DocumentService;
use App\Services\NumberingService;
use App\Services\PdfService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(
        private DocumentService $docService,
        private NumberingService $numbering,
        private PdfService $pdfService
    ) {}

    public function index()
    {
        $company = app('currentCompany');
        $invoices = $company->invoices()
            ->with('client')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('manager.invoices.index', compact('company', 'invoices'));
    }

    public function create()
    {
        $company = app('currentCompany');
        $clients = $company->clients()->orderBy('company_name')->get();
        return view('manager.invoices.create', compact('company', 'clients'));
    }

    public function store(Request $request)
    {
        $company = app('currentCompany');
        $data = $this->validateInvoice($request);
        $items = $request->input('items', []);

        $totals = $this->docService->calculateTotals(
            $items,
            (float) $data['tva_rate'],
            $data['discount_percent'] ? (float) $data['discount_percent'] : null
        );

        $data['number'] = $this->numbering->generate($company, 'F');
        $data['company_id'] = $company->id;
        $data = array_merge($data, $totals);

        $invoice = Invoice::create($data);
        $this->docService->saveItems('invoice', $invoice->id, $items, $company);

        return redirect()->route('manager.invoices.show', $invoice)
            ->with('success', 'Facture ' . $invoice->number . ' créée avec succès.');
    }

    public function storeFromSource(Request $request)
    {
        $company = app('currentCompany');
        $data = $this->validateInvoice($request);
        $items = $request->input('items', []);

        $totals = $this->docService->calculateTotals(
            $items,
            (float) $data['tva_rate'],
            $data['discount_percent'] ? (float) $data['discount_percent'] : null
        );

        $data['number'] = $this->numbering->generate($company, 'F');
        $data['company_id'] = $company->id;
        $data['source_type'] = $request->input('source_type');
        $data['source_id'] = $request->input('source_id');
        $data = array_merge($data, $totals);

        $invoice = Invoice::create($data);
        $this->docService->saveItems('invoice', $invoice->id, $items, $company);

        if ($data['source_type'] === 'quote') {
            Quote::find($data['source_id'])?->update(['status' => 'converted']);
        } elseif ($data['source_type'] === 'purchase_order') {
            PurchaseOrder::find($data['source_id'])?->update(['status' => 'converted']);
        }

        return redirect()->route('manager.invoices.show', $invoice)
            ->with('success', 'Facture ' . $invoice->number . ' créée avec succès.');
    }

    public function show(Invoice $invoice)
    {
        $company = app('currentCompany');
        abort_if($invoice->company_id !== $company->id, 403);

        $invoice->load(['client', 'items', 'payments']);
        return view('manager.invoices.show', compact('company', 'invoice'));
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $company = app('currentCompany');
        abort_if($invoice->company_id !== $company->id, 403);

        $request->validate(['status' => 'required|in:draft,sent,signed,archived']);
        $invoice->update(['status' => $request->status]);

        return back()->with('success', 'Statut mis à jour.');
    }

    public function edit(Invoice $invoice)
    {
        $company = app('currentCompany');
        abort_if($invoice->company_id !== $company->id, 403);

        $clients = $company->clients()->orderBy('company_name')->get();
        $invoice->load('items');
        return view('manager.invoices.edit', compact('company', 'invoice', 'clients'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $company = app('currentCompany');
        abort_if($invoice->company_id !== $company->id, 403);

        $data = $this->validateInvoice($request);
        $items = $request->input('items', []);

        $totals = $this->docService->calculateTotals(
            $items,
            (float) $data['tva_rate'],
            $data['discount_percent'] ? (float) $data['discount_percent'] : null
        );

        $data = array_merge($data, $totals);
        $invoice->update($data);
        $this->docService->saveItems('invoice', $invoice->id, $items, $company);

        return redirect()->route('manager.invoices.show', $invoice)
            ->with('success', 'Facture mise à jour avec succès.');
    }

    public function destroy(Invoice $invoice)
    {
        $company = app('currentCompany');
        abort_if($invoice->company_id !== $company->id, 403);

        $invoice->items()->delete();
        $invoice->delete();

        return redirect()->route('manager.invoices.index')
            ->with('success', 'Facture supprimée avec succès.');
    }

    public function duplicate(Invoice $invoice)
    {
        $company = app('currentCompany');
        abort_if($invoice->company_id !== $company->id, 403);

        $invoice->load('items');

        // Copie de l'en-tête : nouveau numéro, statut brouillon, règlements réinitialisés
        $copy = $invoice->replicate();
        $copy->number         = $this->numbering->generate($company, 'F');
        $copy->status         = 'draft';
        $copy->date           = now()->toDateString();
        $copy->paid_amount    = 0;
        $copy->payment_status = 'unpaid';
        $copy->source_type    = null;
        $copy->source_id      = null;
        $copy->save();

        // Copie des lignes (prestations + catégories)
        foreach ($invoice->items as $item) {
            $newItem = $item->replicate();
            $newItem->document_id = $copy->id;
            $newItem->save();
        }

        return redirect()->route('manager.invoices.edit', $copy)
            ->with('success', 'Facture dupliquée depuis ' . $invoice->number . '. Ajustez la date / période puis enregistrez.');
    }

    public function pdf(Invoice $invoice)
    {
        $company = app('currentCompany');
        abort_if($invoice->company_id !== $company->id, 403);

        $pdf = $this->pdfService->generateInvoice($invoice);
        $filename = 'Facture_' . str_replace('/', '-', $invoice->number) . '.pdf';

        return $pdf->download($filename);
    }

    private function validateInvoice(Request $request): array
    {
        return $request->validate([
            'client_id'       => 'required|exists:clients,id',
            'date'            => 'required|date',
            'marche_number'   => 'nullable|string|max:100',
            'period_start'    => 'nullable|date',
            'period_end'      => 'nullable|date|after_or_equal:period_start',
            'payment_mode'    => 'nullable|string|max:255',
            'subject'         => 'nullable|string|max:500',
            'tva_rate'        => 'required|numeric|min:0|max:100',
            'discount_percent'=> 'nullable|numeric|min:0|max:100',
            'notes'           => 'nullable|string|max:2000',
        ], [
            'client_id.required' => 'Veuillez sélectionner un client.',
            'date.required'      => 'La date est obligatoire.',
            'tva_rate.required'  => 'Le taux TVA est obligatoire.',
        ]);
    }
}
