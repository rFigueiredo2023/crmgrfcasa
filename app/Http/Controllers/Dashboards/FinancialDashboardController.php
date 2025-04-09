<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialDashboardController extends Controller
{
    /**
     * Exibe o dashboard financeiro.
     */
    public function index(): View
    {
        if (!auth()->check() || auth()->user()->role !== 'financeiro') {
            abort(403, 'Acesso não autorizado.');
        }

        return view('dashboards.financeiro');
    }
}
