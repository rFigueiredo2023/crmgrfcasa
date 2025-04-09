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
      switch ($user->role) {
          case 'admin':
              return redirect()->intended('/admin');
          case 'vendas':
              return redirect()->intended('/vendas');
          case 'financeiro':
              return redirect()->intended('/financeiro');
          default:
              return redirect('/login');
      }
  }
}
