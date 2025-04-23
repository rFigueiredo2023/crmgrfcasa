<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'is_admin',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
  ];

  /**
   * Verifica se o usuário é Admin.
   *
   * @return bool
   */
  public function isAdmin(): bool
  {
      return $this->is_admin || $this->role === 'admin';
  }

  /**
   * Verifica se o usuário é de Vendas.
   *
   * @return bool
   */
  public function isVendas(): bool
  {
      return $this->role === 'vendas';
  }

  /**
   * Verifica se o usuário é do Financeiro.
   *
   * @return bool
   */
  public function isFinancial(): bool
  {
      return $this->role === 'financial';
  }

  /**
   * Check if user has a specific role
   */
  public function hasRole($role)
  {
    return $this->role === $role;
  }
}
