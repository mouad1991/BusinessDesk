<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ServiceDescription;
use Illuminate\Http\Request;

class ServiceDescriptionController extends Controller
{
    public function autocomplete(Request $request)
    {
        $company = app('currentCompany');

        $term = $request->input('q', '');
        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $results = ServiceDescription::where('company_id', $company->id)
            ->where('description', 'like', '%' . $term . '%')
            ->orderBy('usage_count', 'desc')
            ->limit(10)
            ->pluck('description');

        return response()->json($results);
    }
}
