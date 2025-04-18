<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Retorna lista de usuários para uso em select boxes
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $users = User::select(['id', 'name'])
                ->orderBy('name')
                ->get();

            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar usuários: ' . $e->getMessage()], 500);
        }
    }
}
