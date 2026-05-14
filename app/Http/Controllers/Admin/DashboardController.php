<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $managers = User::where('role', 'manager')
            ->withCount('companies')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.dashboard', compact('managers'));
    }
}
