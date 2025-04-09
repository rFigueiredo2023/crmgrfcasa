<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomePage extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            $role = auth()->user()->role;
            
            switch ($role) {
                case 'admin':
                    return redirect()->route('dashboard.admin');
                case 'vendas':
                    return redirect()->route('dashboard.vendas');
                case 'financeiro':
                    return redirect()->route('dashboard.financeiro');
                default:
                    return view('content.pages.pages-home');
            }
        }
        
        return redirect()->route('login');
    }
}
