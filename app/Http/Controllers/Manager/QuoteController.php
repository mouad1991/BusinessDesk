<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Services\DocumentService;
use App\Services\NumberingService;
use App\Services\PdfService;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function __construct(
        private DocumentService $docService,
        private NumberingService $numbering,
        private PdfService $pdfService
    ) {}

    public function index()
    {
        $company = app('currentCompany');
        $quotes = $company->quotes()
            ->with('client')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('manager.quotes.index', compact('company', 'quotes'));
    }

    public function create()
    {
        $company = app('currentCompany');
        $clients = $company->clients()->orderBy('company_name')->get();
        return view('manager.quotes.create', compact('company', 'clients'));
    }

    public function store(Request $request)
    {
        $company = app('currentCompany');
        $data = $this->validateQuote($request);
        $items = $request->input('items', []);

        $totals = $this->docService->calculateTotals(
            $items,
            (float) $data['tva_rate'],
            $data['discount_percent'] ? (float) $data['discount_percent'] : null
        );

        $data['number'] = $this->numbering->generate($company, 'D');
        $data['company_id'] = $company->id;
        $data = array_merge($data, $totals);

        $quote = Quote::create($data);
        $this->docService->saveItems('quote', $quote->id, $items, $company);

        return redirect()->route('manager.quotes.show', $quote)
            ->with('success', 'Devis ' . $quote->number . ' créé avec succès.');
    }

    public function show(Quote $quote)
    {
        $company = app('currentCompany');
        abort_if($quote->company_id !== $company->id, 403);

        $quote->load(['client', 'items']);
        return view('manager.quotes.show', compact('company', 'quote'));
    }

    public function edit(Quote $quote)
    {
        $company = app('currentCompany');
        abort_if($quote->company_id !== $company->id, 403);

        $clients = $company->clients()->orderBy('company_name')->get();
        $quote->load('items');
        return view('manager.quotes.edit', compact('company', 'quote', 'clients'));
    }

    public function update(Request $request, Quote $quote)
    {
        $company = app('currentCompany');
        abort_if($quote->company_id !== $company->id, 403);

        $data = $this->validateQuote($request);
        $items = $request->input('items', []);

        $totals = $this->docService->calculateTotals(
            $items,
            (float) $data['tva_rate'],
            $data['discount_percent'] ? (float) $data['discount_percent'] : null
        );

        $data = array_merge($data, $totals);
        $quote->update($data);
        $this->docService->saveItems('quote', $quote->id, $items, $company);

        return redirect()->route('manager.quotes.show', $quote)
            ->with('success', 'Devis mis à jour avec succès.');
    }

    public function destroy(Quote $quote)
    {
        $company = app('currentCompany');
        abort_if($quote->company_id !== $company->id, 403);

        $quote->items()->delete();
        $quote->delete();

        return redirect()->route('manager.quotes.index')
            ->with('success', 'Devis supprimé avec succès.');
    }

    public function pdf(Quote $quote)
    {
        $company = app('currentCompany');
        abort_if($quote->company_id !== $company->id, 403);

        $pdf = $this->pdfService->generateQuote($quote);
        $filename = 'Devis_' . str_replace('/', '-', $quote->number) . '.pdf';

        return $pdf->download($filename);
    }

    public function updateStatus(Request $request, Quote $quote)
    {
        $company = app('currentCompany');
        abort_if($quote->company_id !== $company->id, 403);

        $request->validate(['status' => 'required|in:draft,sent,signed,archived']);
        $quote->update(['status' => $request->status]);

        return back()->with('success', 'Statut mis à jour.');
    }

    public function convertToInvoice(Quote $quote)
    {
        $company = app('currentCompany');
        abort_if($quote->company_id !== $company->id, 403);

        $quote->load(['client', 'items']);
        $clients = $company->clients()->orderBy('company_name')->get();

        return view('manager.invoices.convert', compact('company', 'quote', 'clients'));
    }

    private function validateQuote(Request $request): array
    {
        return $request->validate([
            'client_id'       => 'required|exists:clients,id',
            'date'            => 'required|date',
            'validity_days'   => 'required|integer|min:1|max:365',
            'payment_mode'    => 'nullable|string|max:255',
            'subject'         => 'required|string|max:500',
            'tva_rate'        => 'required|numeric|min:0|max:100',
            'discount_percent'=> 'nullable|numeric|min:0|max:100',
            'notes'           => 'nullable|string|max:2000',
        ], [
            'client_id.required' => 'Veuillez sélectionner un client.',
            'date.required'      => 'La date est obligatoire.',
            'subject.required'   => 'L\'objet est obligatoire.',
            'tva_rate.required'  => 'Le taux TVA est obligatoire.',
        ]);
    }
}
