<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\DeliveryNote;
use App\Models\DeliveryItem;
use App\Services\NumberingService;
use App\Services\PdfService;
use Illuminate\Http\Request;

class DeliveryNoteController extends Controller
{
    public function __construct(
        private NumberingService $numbering,
        private PdfService $pdfService
    ) {}

    public function index()
    {
        $company = app('currentCompany');
        $notes = $company->deliveryNotes()
            ->with('client')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('manager.delivery_notes.index', compact('company', 'notes'));
    }

    public function create()
    {
        $company = app('currentCompany');
        $clients = $company->clients()->orderBy('company_name')->get();
        return view('manager.delivery_notes.create', compact('company', 'clients'));
    }

    public function store(Request $request)
    {
        $company = app('currentCompany');
        $data = $this->validateNote($request);
        $data['number'] = $this->numbering->generate($company, 'BL');
        $data['company_id'] = $company->id;

        $note = DeliveryNote::create($data);
        $this->saveDeliveryItems($note, $request->input('items', []));

        return redirect()->route('manager.delivery-notes.show', $note)
            ->with('success', 'Bon de livraison ' . $note->number . ' créé avec succès.');
    }

    public function show(DeliveryNote $deliveryNote)
    {
        $company = app('currentCompany');
        abort_if($deliveryNote->company_id !== $company->id, 403);

        $deliveryNote->load(['client', 'items']);
        return view('manager.delivery_notes.show', compact('company', 'deliveryNote'));
    }

    public function updateStatus(Request $request, DeliveryNote $deliveryNote)
    {
        $company = app('currentCompany');
        abort_if($deliveryNote->company_id !== $company->id, 403);

        $request->validate(['status' => 'required|in:draft,sent,signed,archived']);
        $deliveryNote->update(['status' => $request->status]);

        return back()->with('success', 'Statut mis à jour.');
    }

    public function edit(DeliveryNote $deliveryNote)
    {
        $company = app('currentCompany');
        abort_if($deliveryNote->company_id !== $company->id, 403);

        $clients = $company->clients()->orderBy('company_name')->get();
        $deliveryNote->load('items');
        return view('manager.delivery_notes.edit', compact('company', 'deliveryNote', 'clients'));
    }

    public function update(Request $request, DeliveryNote $deliveryNote)
    {
        $company = app('currentCompany');
        abort_if($deliveryNote->company_id !== $company->id, 403);

        $data = $this->validateNote($request);
        $deliveryNote->update($data);
        $this->saveDeliveryItems($deliveryNote, $request->input('items', []));

        return redirect()->route('manager.delivery-notes.show', $deliveryNote)
            ->with('success', 'Bon de livraison mis à jour avec succès.');
    }

    public function destroy(DeliveryNote $deliveryNote)
    {
        $company = app('currentCompany');
        abort_if($deliveryNote->company_id !== $company->id, 403);

        $deliveryNote->items()->delete();
        $deliveryNote->delete();

        return redirect()->route('manager.delivery-notes.index')
            ->with('success', 'Bon de livraison supprimé avec succès.');
    }

    public function pdf(DeliveryNote $deliveryNote)
    {
        $company = app('currentCompany');
        abort_if($deliveryNote->company_id !== $company->id, 403);

        $pdf = $this->pdfService->generateDeliveryNote($deliveryNote);
        $filename = 'BL_' . str_replace('/', '-', $deliveryNote->number) . '.pdf';

        return $pdf->download($filename);
    }

    private function saveDeliveryItems(DeliveryNote $note, array $items): void
    {
        $note->items()->delete();
        $order = 1;
        foreach ($items as $item) {
            if (empty($item['description'])) continue;

            $qtyOrdered  = (float)($item['qty_ordered'] ?? 1);
            $qtyShipped  = (float)($item['qty_shipped'] ?? 0);
            $qtyPending  = max(0, $qtyOrdered - $qtyShipped);

            DeliveryItem::create([
                'delivery_note_id' => $note->id,
                'ref'              => str_pad($order, 2, '0', STR_PAD_LEFT),
                'description'      => strip_tags($item['description'], '<strong><b><em><i><u><br><ul><ol><li><p><div><span>'),
                'unit'             => $item['unit'] ?? 'Unité',
                'qty_ordered'      => $qtyOrdered,
                'qty_shipped'      => $qtyShipped,
                'qty_pending'      => $qtyPending,
                'sort_order'       => $order,
            ]);
            $order++;
        }
    }

    private function validateNote(Request $request): array
    {
        return $request->validate([
            'client_id'        => 'required|exists:clients,id',
            'date'             => 'required|date',
            'order_reference'  => 'nullable|string|max:100',
            'delivery_address' => 'nullable|string|max:500',
            'quote_reference'  => 'nullable|string|max:100',
            'notes'            => 'nullable|string|max:2000',
        ], [
            'client_id.required' => 'Veuillez sélectionner un client.',
            'date.required'      => 'La date est obligatoire.',
        ]);
    }
}
