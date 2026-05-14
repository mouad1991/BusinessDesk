<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $company = app('currentCompany');
        $clients = $company->clients()->orderBy('company_name')->paginate(20);

        return view('manager.clients.index', compact('company', 'clients'));
    }

    public function create()
    {
        $company = app('currentCompany');
        return view('manager.clients.create', compact('company'));
    }

    public function store(Request $request)
    {
        $company = app('currentCompany');

        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'address'      => 'nullable|string|max:500',
            'phone'        => 'nullable|string|max:30',
            'ice'          => 'nullable|string|max:50',
        ], [
            'company_name.required' => 'Le nom de la société client est obligatoire.',
        ]);

        $data['company_id'] = $company->id;
        Client::create($data);

        return redirect()->route('manager.clients.index')
            ->with('success', 'Client créé avec succès.');
    }

    public function edit(Client $client)
    {
        $company = app('currentCompany');
        abort_if($client->company_id !== $company->id, 403);

        return view('manager.clients.edit', compact('company', 'client'));
    }

    public function update(Request $request, Client $client)
    {
        $company = app('currentCompany');
        abort_if($client->company_id !== $company->id, 403);

        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'address'      => 'nullable|string|max:500',
            'phone'        => 'nullable|string|max:30',
            'ice'          => 'nullable|string|max:50',
        ]);

        $client->update($data);

        return redirect()->route('manager.clients.index')
            ->with('success', 'Client modifié avec succès.');
    }

    public function destroy(Client $client)
    {
        $company = app('currentCompany');
        abort_if($client->company_id !== $company->id, 403);

        $client->delete();

        return redirect()->route('manager.clients.index')
            ->with('success', 'Client supprimé avec succès.');
    }
}
