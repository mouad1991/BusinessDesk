<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Services\DocumentService;
use App\Services\NumberingService;
use App\Services\PdfService;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function __construct(
        private DocumentService $docService,
        private NumberingService $numbering,
        private PdfService $pdfService
    ) {}

    public function index()
    {
        $company = app('currentCompany');
        $orders = $company->purchaseOrders()
            ->with('client')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('manager.purchase_orders.index', compact('company', 'orders'));
    }

    public function create()
    {
        $company = app('currentCompany');
        $clients = $company->clients()->orderBy('company_name')->get();
        return view('manager.purchase_orders.create', compact('company', 'clients'));
    }

    public function store(Request $request)
    {
        $company = app('currentCompany');
        $data = $this->validateOrder($request);
        $items = $request->input('items', []);

        $totals = $this->docService->calculateTotals($items, (float) $data['tva_rate'], null);

        $data['number'] = $this->numbering->generate($company, 'BC');
        $data['company_id'] = $company->id;
        $data['total_ht']   = $totals['total_ht'];
        $data['tva_amount'] = $totals['tva_amount'];
        $data['total_ttc']  = $totals['total_ttc'];
        $data['amount_in_words'] = $totals['amount_in_words'];

        $order = PurchaseOrder::create($data);
        $this->docService->saveItems('purchase_order', $order->id, $items, $company);

        return redirect()->route('manager.purchase-orders.show', $order)
            ->with('success', 'Bon de commande ' . $order->number . ' créé avec succès.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $company = app('currentCompany');
        abort_if($purchaseOrder->company_id !== $company->id, 403);

        $purchaseOrder->load(['client', 'items']);
        return view('manager.purchase_orders.show', compact('company', 'purchaseOrder'));
    }

    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $company = app('currentCompany');
        abort_if($purchaseOrder->company_id !== $company->id, 403);

        $request->validate(['status' => 'required|in:draft,sent,signed,archived']);
        $purchaseOrder->update(['status' => $request->status]);

        return back()->with('success', 'Statut mis à jour.');
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $company = app('currentCompany');
        abort_if($purchaseOrder->company_id !== $company->id, 403);

        $clients = $company->clients()->orderBy('company_name')->get();
        $purchaseOrder->load('items');
        return view('manager.purchase_orders.edit', compact('company', 'purchaseOrder', 'clients'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $company = app('currentCompany');
        abort_if($purchaseOrder->company_id !== $company->id, 403);

        $data = $this->validateOrder($request);
        $items = $request->input('items', []);

        $totals = $this->docService->calculateTotals($items, (float) $data['tva_rate'], null);

        $data['total_ht']        = $totals['total_ht'];
        $data['tva_amount']      = $totals['tva_amount'];
        $data['total_ttc']       = $totals['total_ttc'];
        $data['amount_in_words'] = $totals['amount_in_words'];

        $purchaseOrder->update($data);
        $this->docService->saveItems('purchase_order', $purchaseOrder->id, $items, $company);

        return redirect()->route('manager.purchase-orders.show', $purchaseOrder)
            ->with('success', 'Bon de commande mis à jour avec succès.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $company = app('currentCompany');
        abort_if($purchaseOrder->company_id !== $company->id, 403);

        $purchaseOrder->items()->delete();
        $purchaseOrder->delete();

        return redirect()->route('manager.purchase-orders.index')
            ->with('success', 'Bon de commande supprimé avec succès.');
    }

    public function pdf(PurchaseOrder $purchaseOrder)
    {
        $company = app('currentCompany');
        abort_if($purchaseOrder->company_id !== $company->id, 403);

        $pdf = $this->pdfService->generatePurchaseOrder($purchaseOrder);
        $filename = 'BC_' . str_replace('/', '-', $purchaseOrder->number) . '.pdf';

        return $pdf->download($filename);
    }

    public function convertToInvoice(PurchaseOrder $purchaseOrder)
    {
        $company = app('currentCompany');
        abort_if($purchaseOrder->company_id !== $company->id, 403);

        $purchaseOrder->load(['client', 'items']);
        $clients = $company->clients()->orderBy('company_name')->get();

        return view('manager.invoices.convert', compact('company', 'purchaseOrder', 'clients'));
    }

    private function validateOrder(Request $request): array
    {
        return $request->validate([
            'client_id'       => 'required|exists:clients,id',
            'date'            => 'required|date',
            'quote_reference' => 'nullable|string|max:100',
            'order_date'      => 'nullable|date',
            'payment_mode'    => 'nullable|string|max:255',
            'subject'         => 'nullable|string|max:500',
            'tva_rate'        => 'required|numeric|min:0|max:100',
            'notes'           => 'nullable|string|max:2000',
        ], [
            'client_id.required' => 'Veuillez sélectionner un client.',
            'date.required'      => 'La date est obligatoire.',
            'tva_rate.required'  => 'Le taux TVA est obligatoire.',
        ]);
    }
}
