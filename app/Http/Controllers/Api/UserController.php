<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Lista os usuários do sistema.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $usuarios = User::whereIn('role', ['admin', 'vendas'])
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($usuarios);
    }
}
