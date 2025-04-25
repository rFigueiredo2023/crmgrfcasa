<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Lista os usuários com função de vendas ou admin.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listarUsuariosVendas()
    {
        $usuarios = User::whereIn('role', ['admin', 'vendas'])
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($usuarios);
    }
}
