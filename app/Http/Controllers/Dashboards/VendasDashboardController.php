<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class VendasDashboardController extends Controller
{
    /**
     * Exibe o dashboard de vendas.
     */
    public function index(): View
    {
        $user = auth()->user();
        Log::info('Acessando dashboard de vendas', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role
        ]);

        return view('content.pages.dashboard-vendas');
    }
}
