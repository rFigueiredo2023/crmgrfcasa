<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;

class LoginBasic extends Controller
{
  public function index()
  {
    if (Auth::check()) {
      return $this->redirectBasedOnRole(Auth::user());
    }

    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.authentications.auth-login-basic', ['pageConfigs' => $pageConfigs]);
  }

  /**
   * Handle an authentication attempt.
   */
  public function login(Request $request)
  {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return $this->redirectBasedOnRole(Auth::user());
    }

    return back()->withErrors([
        'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
    ])->withInput($request->only('email'));
  }

  /**
   * Log the user out of the application.
   */
  public function logout(Request $request)
  {
      Auth::logout();
      $request->session()->invalidate();
      $request->session()->regenerateToken();
      return redirect('/login');
  }

  /**
   * Redirect the user based on their role.
   */
  protected function redirectBasedOnRole($user): RedirectResponse
  {
      // Verificação adicional para evitar redirecionamento em loop
      if (empty($user->role)) {
          // Se não tiver role, manda para uma página padrão
          return redirect('/atendimentos');
      }

      switch ($user->role) {
          case 'admin':
              return redirect()->route('dashboard.admin');
          case 'vendas':
              return redirect()->route('dashboard.vendas');
          case 'financeiro':
              return redirect()->route('dashboard.financeiro');
          default:
              // Em vez de redirecionar para login (que causa loop),
              // redirecionamos para uma página padrão
              return redirect('/atendimentos');
      }
  }
}
