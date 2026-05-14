<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMarketRequest;
use App\Http\Requests\UpdateMarketRequest;
use App\Models\Market;
use App\Models\MarketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarketController extends Controller
{
    public function index(Request $request)
    {
        $company = app('currentCompany');

        $clients = $company->clients()->orderBy('company_name')->get();

        $markets = $company->markets()
            ->with('client')
            ->when($request->search, fn($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->when($request->status,      fn($q) => $q->where('status', $request->status))
            ->when($request->market_type, fn($q) => $q->where('market_type', $request->market_type))
            ->when($request->client_id,   fn($q) => $q->where('client_id', $request->client_id))
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('manager.markets.index', compact('company', 'markets', 'clients'));
    }

    public function create()
    {
        $company = app('currentCompany');

        $clients    = $company->clients()->orderBy('company_name')->get();
        $statuses   = Market::statusOptions();
        $types      = Market::typeOptions();
        $categories = Market::categoryOptions();
        $cautionStatuses = Market::cautionStatusOptions();

        return view('manager.markets.create',
            compact('company', 'clients', 'statuses', 'types', 'categories', 'cautionStatuses'));
    }

    public function store(StoreMarketRequest $request)
    {
        $company = app('currentCompany');

        $data = $request->validated();
        $data['company_id']      = $company->id;
        $data['invoiced_amount'] = $data['invoiced_amount'] ?? 0;
        $data['paid_amount']     = $data['paid_amount']     ?? 0;
        unset($data['attachments']);

        $market = Market::create($data);

        $this->handleAttachments($request, $market);

        return redirect()
            ->route('manager.markets.show', $market)
            ->with('success', 'Marché créé avec succès.');
    }

    public function show(Market $market)
    {
        $company = app('currentCompany');
        abort_if($market->company_id !== $company->id, 403);

        $market->load(['client', 'attachments']);
        return view('manager.markets.show', compact('company', 'market'));
    }

    public function edit(Market $market)
    {
        $company = app('currentCompany');
        abort_if($market->company_id !== $company->id, 403);

        $market->load('attachments');
        $clients         = $company->clients()->orderBy('company_name')->get();
        $statuses        = Market::statusOptions();
        $types           = Market::typeOptions();
        $categories      = Market::categoryOptions();
        $cautionStatuses = Market::cautionStatusOptions();

        return view('manager.markets.edit',
            compact('company', 'market', 'clients', 'statuses', 'types', 'categories', 'cautionStatuses'));
    }

    public function update(UpdateMarketRequest $request, Market $market)
    {
        $data = $request->validated();
        $data['invoiced_amount'] = $data['invoiced_amount'] ?? 0;
        $data['paid_amount']     = $data['paid_amount']     ?? 0;
        unset($data['attachments']);

        $market->update($data);

        $this->handleAttachments($request, $market);

        return redirect()
            ->route('manager.markets.show', $market)
            ->with('success', 'Marché modifié avec succès.');
    }

    public function destroy(Market $market)
    {
        $company = app('currentCompany');
        abort_if($market->company_id !== $company->id, 403);

        foreach ($market->attachments as $attachment) {
            Storage::disk('local')->delete($attachment->file_path);
        }
        $market->delete();

        return redirect()
            ->route('manager.markets.index')
            ->with('success', 'Marché supprimé.');
    }

    public function destroyAttachment(Market $market, MarketAttachment $attachment)
    {
        $company = app('currentCompany');
        abort_if($market->company_id !== $company->id, 403);
        abort_if($attachment->market_id !== $market->id, 403);

        $attachment->delete();

        return back()->with('success', 'Fichier supprimé.');
    }

    public function downloadAttachment(Market $market, MarketAttachment $attachment)
    {
        $company = app('currentCompany');
        abort_if($market->company_id !== $company->id, 403);
        abort_if($attachment->market_id !== $market->id, 403);

        abort_unless(Storage::disk('local')->exists($attachment->file_path), 404);

        return Storage::disk('local')->download($attachment->file_path, $attachment->original_name);
    }

    public function export(Request $request)
    {
        $company = app('currentCompany');

        $markets = $company->markets()
            ->with('client')
            ->when($request->search, fn($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->when($request->status,      fn($q) => $q->where('status', $request->status))
            ->when($request->market_type, fn($q) => $q->where('market_type', $request->market_type))
            ->when($request->client_id,   fn($q) => $q->where('client_id', $request->client_id))
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'Marchés_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $company->name) . '_' . date('Y-m-d') . '.xls';

        $xml = $this->buildXls($markets, $company);

        return response($xml, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
        ]);
    }

    public function updateStatus(Request $request, Market $market)
    {
        $company = app('currentCompany');
        abort_if($market->company_id !== $company->id, 403);

        $request->validate(['status' => 'required|in:' . implode(',', array_keys(Market::statusOptions()))]);
        $market->update(['status' => $request->status]);

        return back()->with('success', 'Statut mis à jour.');
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function handleAttachments(Request $request, Market $market): void
    {
        if (!$request->hasFile('attachments')) return;

        foreach ($request->file('attachments') as $file) {
            $path = $file->store("markets/{$market->company_id}/{$market->id}", 'local');
            $market->attachments()->create([
                'file_path'     => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }
    }

    private function buildXls($markets, $company): string
    {
        $e = fn(string $v) => htmlspecialchars($v, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        $headers = [
            'Objet', 'Client', 'Type', 'Catégorie', 'Statut',
            'Date début', 'Date fin', 'Montant total (DH)',
            'Facturé (DH)', 'Réglé (DH)', 'Reste à encaisser (DH)',
            'Montant caution (DH)', 'Statut caution', 'Notes',
        ];

        $headerRow = implode('', array_map(
            fn($h) => "<Cell ss:StyleID=\"H\"><Data ss:Type=\"String\">{$e($h)}</Data></Cell>",
            $headers
        ));

        $rows = '';
        foreach ($markets as $market) {
            $cells = [
                $market->title,
                $market->client?->company_name ?? '',
                $market->market_type_label,
                $market->category_label,
                $market->status_label,
                $market->start_date?->format('d/m/Y') ?? '',
                $market->end_date?->format('d/m/Y') ?? '',
                number_format((float)$market->total_amount, 2, '.', ''),
                number_format((float)$market->invoiced_amount, 2, '.', ''),
                number_format((float)$market->paid_amount, 2, '.', ''),
                number_format((float)$market->remaining_amount, 2, '.', ''),
                $market->caution_amount ? number_format((float)$market->caution_amount, 2, '.', '') : '',
                $market->caution_status_label ?? '',
                $market->notes ?? '',
            ];

            $rowCells = implode('', array_map(
                fn($c, $i) => '<Cell><Data ss:Type="' . ($i >= 7 && $i <= 11 && is_numeric($c) ? 'Number' : 'String') . '">' . $e((string)$c) . '</Data></Cell>',
                $cells,
                array_keys($cells)
            ));

            $rows .= "<Row>{$rowCells}</Row>\n";
        }

        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
            '<?mso-application progid="Excel.Sheet"?>' . "\n" .
            '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n" .
            ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n" .
            '<Styles><Style ss:ID="H"><Font ss:Bold="1"/></Style></Styles>' . "\n" .
            '<Worksheet ss:Name="Marchés"><Table>' . "\n" .
            "<Row>{$headerRow}</Row>\n" .
            $rows .
            '</Table></Worksheet></Workbook>';
    }
}
