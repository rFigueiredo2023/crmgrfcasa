<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendasDashboardController extends Controller
{
    /**
     * Exibe o dashboard de vendas.
     */
    public function index(): View
    {
        if (!auth()->check() || auth()->user()->role !== 'vendas') {
            abort(403, 'Acesso não autorizado.');
        }

        return view('dashboards.vendas');
    }
}
