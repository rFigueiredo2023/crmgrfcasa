<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Exibe o dashboard do admin.
     */
    public function index(): View
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Acesso não autorizado.');
        }

        return view('dashboards.admin');
    }
}
